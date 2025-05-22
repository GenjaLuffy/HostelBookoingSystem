<?php
include './includes/header.php';
include './includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error_message = "";

// Fetch user data function
function fetchUserData($con, $user_id)
{
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$user = fetchUserData($con, $user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $gender = $_POST['gender'];
    $type = $_POST['type'];

    // Username check
    $stmt = $con->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $error_message = "This username is already taken.";
    }
    $stmt->close();

    // Email check
    if (!$error_message) {
        $stmt = $con->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error_message = "This email is already registered.";
        }
        $stmt->close();
    }

    // Image upload
    $imageName = null;
    if (!$error_message && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $newFileName = md5(time() . $_FILES['profile_picture']['name']) . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $error_message = "Failed to create upload directory.";
                }
            }
            $dest = $uploadDir . $newFileName;
            if (!$error_message && move_uploaded_file($_FILES['profile_picture']['tmp_name'], $dest)) {
                $imageName = $newFileName;
            } else if (!$error_message) {
                $error_message = "Failed to move uploaded file.";
            }
        } else {
            $error_message = "Invalid file type.";
        }
    }

    // Update
    if (!$error_message) {
        $params = [$name, $username, $email, $dob, $phone, $address, $gender, $type];
        $setParts = ["name = ?", "username = ?", "email = ?", "dob = ?", "phone = ?", "address = ?", "gender = ?", "type = ?"];
        if (!empty($password)) {
            $setParts[] = "password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        if ($imageName) {
            $setParts[] = "profile_picture = ?";
            $params[] = $imageName;
        }
        $params[] = $user_id;

        $sql = "UPDATE users SET " . implode(", ", $setParts) . " WHERE id = ?";
        $stmt = $con->prepare($sql);
        $types = str_repeat("s", count($params) - 1) . "i";
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            // Redirect after successful update
            header("Location: userProfile.php");
            exit();
        } else {
            $error_message = "Failed to update: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<main class="main-content">
    <h1>Edit Profile</h1>

    <?php if ($error_message): ?>
        <p style="color:red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <div class="form-card">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile-picture-input">Profile Picture</label>
                <div class="profile-pic" id="profile-pic-preview" tabindex="0">
                    <?php
                    $profilePic = !empty($user['profile_picture']) && file_exists(__DIR__ . '/uploads/' . $user['profile_picture'])
                        ? 'uploads/' . $user['profile_picture']
                        : null;
                    ?>
                    <?php if ($profilePic): ?>
                        <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture" />
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                    <div class="edit-overlay">Edit</div>
                    <input type="file" name="profile_picture" id="profile-picture-input" accept="image/*" style="display:none;" />
                </div>
            </div>

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required />
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required />
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required />
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password" />
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']) ?>" required />
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" />
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" />
            </div>

            <div class="form-group">
                <label>Gender</label>
                <select name="gender" required>
                    <option value="male" <?= $user['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $user['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= $user['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label>Type</label>
                <select name="type" required>
                    <option value="student" <?= $user['type'] === 'student' ? 'selected' : '' ?>>Student</option>
                </select>
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

    profilePicDiv.addEventListener('click', () => fileInput.click());

    profilePicDiv.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            fileInput.click();
        }
    });

    fileInput.addEventListener('change', event => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                let img = profilePicDiv.querySelector('img');
                if (!img) {
                    profilePicDiv.innerHTML = '';
                    img = document.createElement('img');
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
