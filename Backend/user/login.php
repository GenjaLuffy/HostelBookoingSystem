<?php
include './includes/connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = trim($_POST["email_or_username"]);
    $password = $_POST["password"];

    // Check users table (hashed password)
    $stmt = $con->prepare("SELECT id, username, password, 'user' as role FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email_or_username, $email_or_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hashedPassword, $role);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $role;
            echo "<script>alert('Login successful as user!'); window.location.href = 'index.php';</script>";
            exit;
        } else {
            echo "<script>alert('Invalid password.'); window.history.back();</script>";
            exit;
        }
    }
    $stmt->close();

    // Check admins table (plain text password)
    $stmt = $con->prepare("SELECT id, username, password, role FROM admins WHERE username = ?");
    $stmt->bind_param("s", $email_or_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $storedPassword, $role);
        $stmt->fetch();

        if ($password === $storedPassword) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $role;

            if ($role === 'superadmin') {
                echo "<script>alert('Login successful as superadmin!'); window.location.href = '../admin/addRoom.php';</script>";
            } else {
                echo "<script>alert('Login successful as admin!'); window.location.href = '../admin/index.php';</script>";
            }
            exit;
        } else {
            echo "<script>alert('Invalid password.'); window.history.back();</script>";
            exit;
        }
    }
    $stmt->close();

    echo "<script>alert('No user found with that email or username.'); window.history.back();</script>";
    $con->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Book Mate</title>
    <link rel="stylesheet" href="./assets/css/login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>

<div class="login-container">
    <button class="google-login">
        <img src="./assets/images/google.png" alt="Google Logo" class="google-icon" />
        LOGIN USING GOOGLE
    </button>

    <div class="divider">OR</div>

    <form action="login.php" method="post">
        <input type="text" name="email_or_username" placeholder="Email or Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <div class="forgot-password">
            <a href="#">Forgot Password?</a>
        </div>
        <button type="submit" class="login-btn">LOG IN</button>
    </form>

    <div class="signup-link">
        Don’t have an account? <a href="register.html">Sign Up</a>
    </div>
</div>

</body>
</html>
