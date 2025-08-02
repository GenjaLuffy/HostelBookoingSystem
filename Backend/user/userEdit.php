<?php
include 'includes/header.php';
include 'includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Fetch current user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $gender = $_POST['gender'];
    $type = $_POST['type'];

    if (empty($name) || empty($username) || empty($email)) {
        $error = "Name, Username, and Email are required.";
    } else {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
            $fileName = $_FILES['profile_picture']['name'];
            $fileSize = $_FILES['profile_picture']['size'];
            $fileType = $_FILES['profile_picture']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadFileDir = './uploads/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    if (!empty($user['profile_picture']) && file_exists($uploadFileDir . $user['profile_picture'])) {
                        unlink($uploadFileDir . $user['profile_picture']);
                    }
                    $profile_picture = $newFileName;
                } else {
                    $error = "There was an error uploading the profile picture.";
                }
            } else {
                $error = "Upload failed. Allowed file types: " . implode(", ", $allowedExtensions);
            }
        } else {
            $profile_picture = $user['profile_picture'];
        }
    }

    if (!$error) {
        $update_sql = "UPDATE users SET name = ?, username = ?, email = ?, dob = ?, phone = ?, address = ?, gender = ?, type = ?, profile_picture = ? WHERE id = ?";
        $stmt_update = $con->prepare($update_sql);
        $stmt_update->bind_param("sssssssssi", $name, $username, $email, $dob, $phone, $address, $gender, $type, $profile_picture, $user_id);

        if ($stmt_update->execute()) {
            $success = "Profile updated successfully!";
            $user['name'] = $name;
            $user['username'] = $username;
            $user['email'] = $email;
            $user['dob'] = $dob;
            $user['phone'] = $phone;
            $user['address'] = $address;
            $user['gender'] = $gender;
            $user['type'] = $type;
            $user['profile_picture'] = $profile_picture;
        } else {
            $error = "Error updating profile: " . $stmt_update->error;
        }
    }
}
?>


<div class="form-container">
    <h2>Edit Profile</h2>
    <!-- Profile picture at the top -->
    <div style="text-align: center; margin-bottom: 25px;">
        <?php if (!empty($user['profile_picture']) && file_exists('./uploads/' . $user['profile_picture'])): ?>
            <img id="profilePreview" src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture" />
        <?php else: ?>
            <div id="profilePreview" class="profile-picture" style="background:#ddd; line-height:120px; color:#aaa; font-size:36px; text-align:center; user-select:none;">N/A</div>
        <?php endif; ?>
    </div>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
        </div>

        <div class="form-group">
            <label for="username">Username *</label>
            <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>

        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender">
                <option value="">Select Gender</option>
                <option value="male" <?php if ($user['gender'] === 'male') echo 'selected'; ?>>Male</option>
                <option value="female" <?php if ($user['gender'] === 'female') echo 'selected'; ?>>Female</option>
                <option value="other" <?php if ($user['gender'] === 'other') echo 'selected'; ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="type">Type</label>
            <select id="type" name="type">
                <option value="">Select Type</option>
                <option value="student" <?php if ($user['type'] === 'student') echo 'selected'; ?>>Student</option>
                <option value="business" <?php if ($user['type'] === 'business') echo 'selected'; ?>>Business</option>
            </select>
        </div>

        <!-- Hidden file input -->
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
        <div class="form-group full-width">
            <button type="submit">Update Profile</button>
        </div>
    </form>
</div>

<script>
    // Trigger file input click when profile picture clicked
    document.getElementById('profilePreview').addEventListener('click', function() {
        document.getElementById('profile_picture').click();
    });

    // Preview uploaded profile picture before submit
    document.getElementById('profile_picture').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('profilePreview');
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.background = 'none'; // remove fallback bg if any
                preview.style.display = 'inline-block';
                preview.textContent = ''; // clear N/A text if any
            };
            reader.readAsDataURL(file);
        }
    });
</script>