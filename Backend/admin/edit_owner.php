<?php
session_start();
include './includes/auth.php';
include './includes/sheader.php';
include './includes/connect.php';

$message = ""; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid owner ID.";
    exit();
}

$admin_id = intval($_GET['id']);

// Fetch current data
$stmt = $con->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Owner not found.";
    exit();
}
$existing_data = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $document_name = $existing_data['document']; // filename stored in DB

    if (!empty($_FILES['document']['name'])) {
        $uploadDir = 'documents/'; // Correct upload directory
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmp = $_FILES['document']['tmp_name'];
        $fileName = basename($_FILES['document']['name']);
        // Create a unique file name to avoid overwrites
        $targetFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
        $targetPath = $uploadDir . $targetFileName;

        $fileType = mime_content_type($fileTmp);
        if (in_array($fileType, ['image/jpeg', 'image/png', 'application/pdf'])) {
            if (move_uploaded_file($fileTmp, $targetPath)) {
                $document_name = $targetFileName; // Save filename only
            } else {
                $message = "<p style='color:red;'>File upload failed.</p>";
            }
        } else {
            $message = "<p style='color:red;'>Only JPG, PNG, and PDF files are allowed.</p>";
        }
    }

    // Only proceed to update if no upload error message set
    if (empty($message)) {
        $updateQuery = "UPDATE admins SET name = ?, phone = ?, document = ? WHERE id = ?";
        $stmt = $con->prepare($updateQuery);
        $stmt->bind_param("sssi", $name, $phone, $document_name, $admin_id);

        if ($stmt->execute()) {
            $message = "<p style='color:green;'>Owner updated successfully.</p>";
            // Refresh the data for form prefill
            $existing_data['name'] = $name;
            $existing_data['phone'] = $phone;
            $existing_data['document'] = $document_name;
        } else {
            $message = "<p style='color:red;'>Update failed.</p>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Owner</title>
<style>
        .main-content {
  width: 100% !important;
  max-width: 500px !important;
  margin: auto;
  background-color: #fff;
  padding: 40px 30px;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
  border: 1px solid #e0e0e0;
}


.main-content h1 {
  text-align: center;
  margin-bottom: 30px;
  color: #333;
  font-size: 26px;
}

form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

label {
  font-weight: 600;
  margin-bottom: 5px;
  color: #555;
}

input[type="text"],
input[type="file"] {
  padding: 10px 14px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 15px;
  transition: border 0.2s;
}

input[type="text"]:focus,
input[type="file"]:focus {
  border-color: #a05ce0;
  outline: none;
}

input[type="submit"] {
  background-color: #8667F2;
  color: white;
  padding: 12px 25px;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
  align-self: flex-start;
}

input[type="submit"]:hover {
  background-color: #654ac6;
}

p {
  font-size: 14px;
  color: #333;
}

a {
  color: #8667F2;
  text-decoration: none;
  font-weight: 500;
}

a:hover {
  text-decoration: underline;
}

img {
  margin-top: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  max-width: 100%;
  height: auto;
}

/* Success & Error messages */
p[style*='color:green'],
p[style*='color:red'] {
  padding: 10px;
  border-radius: 5px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 20px;
}

p[style*='color:green'] {
  background-color: #e6ffe6;
  border: 1px solid #66cc66;
  color: #2e7d32;
}

p[style*='color:red'] {
  background-color: #ffe6e6;
  border: 1px solid #ff6666;
  color: #c62828;
}

/* Responsive */
@media (max-width: 600px) {
  .main-content {
    padding: 30px 20px;
  }

  input[type="submit"] {
    width: 100%;
    text-align: center;
  }
}
    </style>
</head>
<body>
    <main class="main-content">
        <h1>Edit Owner</h1>

        <form action="" method="post" enctype="multipart/form-data">

            <!-- Show message inside form -->
            <?= $message; ?>

            <label for="name">Owner Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($existing_data['name']); ?>" required>

            <label for="phone">Contact Number:</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($existing_data['phone']); ?>" required>

            <label for="document">Upload Image or PDF (optional):</label>
            <input type="file" name="document" id="document" accept=".jpg,.jpeg,.png,.pdf">

            <?php
            if (!empty($existing_data['document'])):
                $documentFile = 'documents/' . $existing_data['document'];
                if (file_exists($documentFile)):
                    $ext = strtolower(pathinfo($documentFile, PATHINFO_EXTENSION));
            ?>
                <p>Current file:
                    <?php if ($ext === 'pdf'): ?>
                        <a href="<?= htmlspecialchars($documentFile); ?>" target="_blank">View PDF</a>
                    <?php else: ?>
                        <br><img src="<?= htmlspecialchars($documentFile); ?>" width="120" alt="Current Image">
                    <?php endif; ?>
                </p>
            <?php
                else:
                    echo "<p style='color:red;'>Current document file not found.</p>";
                endif;
            endif;
            ?>

            <input type="submit" name="update" value="Update" style="margin-top: 10px;">
        </form>
    </main>
</body>
</html>
