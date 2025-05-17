<?php
include './includes/connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $type = $_POST["type"];
    $profilePicture = $_FILES["profile_picture"]["name"];

    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        echo "<script>alert('Name should not contain numbers or special characters.'); window.history.back();</script>";
        exit;
    }

    $checkStmt = $con->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('Username already taken. Please choose another.'); window.history.back();</script>";
        $checkStmt->close();
        exit;
    }
    $checkStmt->close();

    if (!preg_match("/^(97|98)[0-9]{8}$/", $phone)) {
        echo "<script>alert('Invalid Phone number.'); window.history.back();</script>";
        exit;
    }
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }


    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and include at least one uppercase letter, one number, and one special character.'); window.history.back();</script>";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $targetFilePath = $targetDir . basename($profilePicture);
    move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath);

    $stmt = $con->prepare("INSERT INTO users (name, username, email, password, phone, address, dob, gender, type, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $name, $username, $email, $hashedPassword, $phone, $address, $dob, $gender, $type, $targetFilePath);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $con->close();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> | Book Mate</title>
    <link rel="stylesheet" href="./assets/css/register.css">
</head>

<body>
<div class="form-container">
    <form action="register.php" method="post" enctype="multipart/form-data">

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required />
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required />
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required />
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="tel" name="phone" id="phone" required pattern="[0-9]{10}" maxlength="10"
                title="Please enter exactly 10 digits" />
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required />
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required />
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" required />
        </div>
        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="dob" required />
        </div>
        <div class="form-group">
            <label>Gender</label>
            <select name="gender" required>
                <option value="">Select</option>
                <option value="female">Female</option>
                <option value="male">Male</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label>Type</label>
            <select name="type" required>
                <option value="">Select</option>
                <option value="student">Student</option>
                <option value="Business">Business</option>
            </select>
        </div>
        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" name="profile_picture" accept="image/*" />
        </div>
        <div class="full-width">
            <button type="submit">REGISTER</button>
        </div>
    </form>
    <p class="login-link">Already have an account? <a href="login.php">Log In</a></p>
</div>

</body>

</html>