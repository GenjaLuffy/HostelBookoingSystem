<?php
session_start();
include './includes/auth.php';    // Auth check
include './includes/connect.php';
include './includes/sheader.php';

// Only allow superadmin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    echo "<script>alert('Access denied. Only superadmins allowed.'); window.location.href = '../user/login.php';</script>";
    exit;
}

// Helper function to upload files safely and convert images to JPG
function uploadFile($file, $allowedTypes, $uploadDir) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ["error" => "File upload error."];
    }

    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        return ["error" => "Invalid file type."];
    }

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uniqueName = time() . "_" . bin2hex(random_bytes(8));
    $targetFilePath = "";

    if ($fileType === 'application/pdf') {
        // Keep pdf extension
        $targetFilePath = $uploadDir . $uniqueName . ".pdf";
        if (!move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return ["error" => "Error moving uploaded PDF file."];
        }
    } else {
        // Convert image to JPEG and save as .jpg
        switch ($fileType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($file['tmp_name']);
                break;
            default:
                return ["error" => "Unsupported image format."];
        }

        if (!$image) {
            return ["error" => "Failed to process image."];
        }

        $targetFilePath = $uploadDir . $uniqueName . ".jpg";
        // Save as JPEG with quality 85
        if (!imagejpeg($image, $targetFilePath, 85)) {
            imagedestroy($image);
            return ["error" => "Failed to save image as JPG."];
        }
        imagedestroy($image);
    }

    return ["path" => $targetFilePath];
}

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $address = trim($_POST['address']);
    $gender = $_POST['gender'];
    $type = $_POST['type'];

    // Allowed mime types for document
    $allowedDocTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];

    // Upload document
    $document = "";
    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $result = uploadFile($_FILES['document'], $allowedDocTypes, "document/");
        if (isset($result['error'])) {
            $error_message = "Document Error: " . $result['error'];
        } else {
            // Store relative path or filename only, whichever your DB expects
            $document = basename($result['path']);
        }
    }

    if (!$error_message) {
        $stmt = $con->prepare("INSERT INTO admins (name, username, email, password, dob, phone, address, gender, document, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $name, $username, $email, $password, $dob, $phone, $address, $gender, $document, $type);

        if ($stmt->execute()) {
            $success_message = "Admin added successfully!";
        } else {
            $error_message = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Owner</title>
    <link rel="stylesheet" href="assets/css/addAdmin.css">

    <style>
        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form input[type="date"],
        form input[type="tel"],
        form select,
        form input[type="file"] {
            width: 100%;
            padding: 8px 10px;
            font-size: 16px;
            border: 1.5px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        form input[type="file"] {
            padding: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        form input[type="text"]:focus,
        form input[type="email"]:focus,
        form input[type="password"]:focus,
        form input[type="date"]:focus,
        form input[type="tel"]:focus,
        form select:focus,
        form input[type="file"]:focus {
            border-color: #0066cc;
            outline: none;
        }

        .success-message {
            max-width: 650px;
            margin: 20px auto;
            padding: 15px 20px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            font-weight: 600;
        }

        .error-message {
            max-width: 650px;
            margin: 20px auto;
            padding: 15px 20px;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            font-weight: 600;
        }

        .form-card {
            max-width: 650px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
        }

        .form-header {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            color: #4a4a4a;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }

        button[type="submit"] {
            width: 100%;
            background-color: #5a4dff;
            color: white;
            padding: 12px;
            font-size: 18px;
            font-weight: 700;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #4539d3;
        }
    </style>
</head>

<body>
    <main class="main-content">
        <?php if ($success_message): ?>
            <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <div class="form-card">
            <div class="form-header">Add Owner</div>
            <form action="addAdmin.php" method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" type="text" name="name" required />
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" required />
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" name="email" required />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required />
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input id="dob" type="date" name="dob" required />
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" type="tel" name="phone" pattern="[0-9]{10}" maxlength="10" required />
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input id="address" type="text" name="address" required />
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="document">Upload Document (Image or PDF)</label>
                    <input id="document" type="file" name="document" accept="image/*,application/pdf" />
                </div>

                <div class="full-width">
                    <button type="submit" name="submit">Add Admin</button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>
