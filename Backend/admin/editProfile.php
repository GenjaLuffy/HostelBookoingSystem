<?php
session_start();
include './includes/connect.php';
include './includes/header.php';

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Initialize messages
$success_message = "";
$error_message = "";

// Fetch current admin data
function fetchAdminData($con, $user_id)
{
    $sql = "SELECT * FROM admins WHERE id = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $con->errno . ") " . $con->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = null;
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
    }
    $stmt->close();
    return $admin;
}

$admin = fetchAdminData($con, $user_id);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Validate email uniqueness except current user
    $stmtCheck = $con->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
    $stmtCheck->bind_param("si", $email, $user_id);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    if ($resCheck->num_rows > 0) {
        $error_message = "This email is already registered by another admin.";
    }
    $stmtCheck->close();

    if (!$error_message) {
        // Handle profile picture upload
        $imageName = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
            $fileName = $_FILES['profile_picture']['name'];
            $fileSize = $_FILES['profile_picture']['size'];
            $fileType = $_FILES['profile_picture']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = __DIR__ . './uploads/';

                $dest_path = $uploadFileDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $imageName = $newFileName;
                } else {
                    $error_message = 'Error moving the uploaded file.';
                }
            } else {
                $error_message = 'Upload failed. Allowed file types: ' . implode(', ', $allowedfileExtensions);
            }
        }

        if (!$error_message) {
            // Prepare update query
            $setParts = ["name = ?", "email = ?", "dob = ?", "phone = ?", "address = ?"];
            $params = [$name, $email, $dob, $phone, $address];

            if (!empty($password)) {
                $setParts[] = "password = ?";
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $params[] = $hashed;
            }

            if ($imageName) {
                $setParts[] = "profile_picture = ?";
                $params[] = $imageName;
            }

            $params[] = $user_id;

            $sql = "UPDATE admins SET " . implode(", ", $setParts) . " WHERE id = ?";
            $stmt = $con->prepare($sql);

            if (!$stmt) {
                $error_message = "Prepare failed: " . $con->error;
            } else {
                // Calculate types string for bind_param
                $types = str_repeat('s', count($params) - 1) . 'i';
                $stmt->bind_param($types, ...$params);
                if ($stmt->execute()) {
                    header("Location: profile.php");
                    exit();
                } else {
                    $error_message = "Update failed: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile</title>
    <link rel="stylesheet" href="assets/css/editProfile.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
<main class="main-content">

    <?php if ($success_message): ?>
        <div style="color:green; margin-bottom:15px;"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div style="color:red; margin-bottom:15px;"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <div class="form-header">Edit Profile</div>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile-picture-input">Profile Picture</label>
                <div class="profile-pic" id="profile-pic-preview" tabindex="0" style="cursor:pointer; position: relative;">
                    <?php
                    $profilePicPath = !empty($admin['profile_picture']) && file_exists(__DIR__ . '/uploads/' . $admin['profile_picture'])
                        ? 'uploads/' . $admin['profile_picture']
                        : null;
                    ?>
                    <?php if ($profilePicPath): ?>
                        <img src="<?= htmlspecialchars($profilePicPath) ?>" alt="Profile Picture" style="max-width:150px; max-height:150px;" />
                    <?php else: ?>
                        <i class="fas fa-user" style="font-size:100px;"></i>
                    <?php endif; ?>
                    <div class="edit-overlay" style="position:absolute; bottom:5px; right:5px; background:#0008; color:#fff; padding:2px 5px; font-size:14px;">Edit</div>
                </div>
                <input type="file" name="profile_picture" id="profile-picture-input" accept="image/*" style="display:none;" />
            </div>

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($admin['name']); ?>" required />
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($admin['email']); ?>" required />
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password" />
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?= htmlspecialchars($admin['dob']); ?>" required />
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($admin['phone']); ?>" pattern="[0-9]{10}" maxlength="10" required />
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($admin['address']); ?>" required />
            </div>
            <div class="full-width">
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</main>

<script>
    const profilePicDiv = document.getElementById('profile-pic-preview');
    const fileInput = document.getElementById('profile-picture-input');

    profilePicDiv.addEventListener('click', () => {
        fileInput.click();
    });

    profilePicDiv.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            fileInput.click();
        }
    });

    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                let img = profilePicDiv.querySelector('img');
                if (!img) {
                    img = document.createElement('img');
                    profilePicDiv.innerHTML = '';
                    profilePicDiv.appendChild(img);
                    const overlay = document.createElement('div');
                    overlay.className = 'edit-overlay';
                    overlay.textContent = 'Edit';
                    profilePicDiv.appendChild(overlay);
                }
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

</body>

</html>