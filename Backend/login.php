<?php
include './includes/connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email_or_username"]);
    $password = $_POST["password"];

    $stmt = $con->prepare("SELECT id, username, password FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            echo "<script>alert('Login successful!'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Invalid password.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No user found with that email or username.'); window.history.back();</script>";
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
    <title>Login | Book Mate</title>
    <link rel="stylesheet" href="./assets/css/login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>

    <div class="login-container">
        <button class="google-login">
            <img src="pic/google.png" alt="Google Logo" class="google-icon" />
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