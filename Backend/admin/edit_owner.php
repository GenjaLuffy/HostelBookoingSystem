<?php
session_start();
include './includes/auth.php';
include './includes/sheader.php';
include './includes/connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid owner ID.";
    exit();
}

$admin_id = intval($_GET['id']);

// Fetch current data
$stmt = $con->prepare("SELECT name, phone, document_path FROM admins WHERE id = ?");
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
    $document_path = $existing_data['document_path'];

    if (!empty($_FILES['document']['name'])) {
        $uploadDir = 'uploads/documents/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmp = $_FILES['document']['tmp_name'];
        $fileName = basename($_FILES['document']['name']);
        $targetPath = $uploadDir . time() . '_' . $fileName;

        $fileType = mime_content_type($fileTmp);
        if (in_array($fileType, ['image/jpeg', 'image/png', 'application/pdf'])) {
            if (move_uploaded_file($fileTmp, $targetPath)) {
                $document_path = $targetPath;
            } else {
                echo "<p style='color:red;'>File upload failed.</p>";
            }
        } else {
            echo "<p style='color:red;'>Only JPG, PNG, and PDF files are allowed.</p>";
        }
    }

    // Update query
    $updateQuery = "UPDATE admins SET name = ?, phone = ?, document_path = ? WHERE id = ?";
    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param("sssi", $name, $phone, $document_path, $admin_id);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Owner updated successfully.</p>";
        // Refresh the data
        $existing_data['name'] = $name;
        $existing_data['phone'] = $phone;
        $existing_data['document_path'] = $document_path;
    } else {
        echo "<p style='color:red;'>Update failed.</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Owner</title>
    <link rel="stylesheet" href="./assets/css/superprofile.css" />
    <link rel="stylesheet" href="./assets/css/sowner.css" />
</head>
<body>
    <main class="main-content">
        <h1>Edit Owner</h1>

        <form action="" method="post" enctype="multipart/form-data">
            <label for="name">Owner Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($existing_data['name']); ?>" required>

            <label for="phone">Contact Number:</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($existing_data['phone']); ?>" required>

            <label for="document">Upload Image or PDF (optional):</label>
            <input type="file" name="document" id="document" accept=".jpg,.jpeg,.png,.pdf">

            <?php if (!empty($existing_data['document_path'])): ?>
                <p>Current file:
                    <?php if (pathinfo($existing_data['document_path'], PATHINFO_EXTENSION) === 'pdf'): ?>
                        <a href="<?= $existing_data['document_path'] ?>" target="_blank">View PDF</a>
                    <?php else: ?>
                        <br><img src="<?= $existing_data['document_path'] ?>" width="120" alt="Current Image">
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <input type="submit" name="update" value="Update" style="margin-top: 10px;">
        </form>
    </main>
</body>
</html>
