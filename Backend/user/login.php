<?php
session_start();
include './includes/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = trim($_POST["email_or_username"]);
    $password = $_POST["password"];

    // Check in users table
    $stmt = $con->prepare("SELECT id, username, password FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email_or_username, $email_or_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = 'user'; // manually assign role for users
            echo "<script>alert('Login successful as user!'); window.location.href = 'index.php';</script>";
            exit;
        }
    }
    $stmt->close();

    // Check in admins table
    $stmt = $con->prepare("SELECT id, username, password, type FROM admins WHERE email = ? OR username = ?");
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

            if ($role === 'superadmin') {
                echo "<script>alert('Login successful as Superadmin!'); window.location.href = '../admin/super.php';</script>";
            } else {
                echo "<script>alert('Login successful as Admin!'); window.location.href = '../admin/index.php';</script>";
            }
            exit;
        }
    }

    $stmt->close();
    $con->close();

    echo "<script>alert('Invalid credentials or user not found.'); window.history.back();</script>";
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

    <div class="divider">Login</div>

    <form action="login.php" method="post">
        <input type="text" name="email_or_username" placeholder="Email or Username" required />
        <input type="password" name="password" placeholder="Password" required />
       
        <button type="submit" class="login-btn">LOG IN</button>
    </form>

    <div class="signup-link">
        Don’t have an account? <a href="register.php">Sign Up</a>
    </div>
</div>

</body>
</html>
