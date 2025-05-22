<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book Mate</title>
  <link rel="stylesheet" href="./assets/css/style.css" />
  <link rel="stylesheet" href="./assets/css/book.css" />
  <link rel="stylesheet" href="./assets/css/hostel.css" />
  <link rel="stylesheet" href="./assets/css/about.css" />
  <link rel="stylesheet" href="./assets/css/userEdit.css" />
  <link rel="stylesheet" href="./assets/css/userProfile.css" />
  <link rel="stylesheet" href="./assets/css/info.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
  <header>
    <a href="index.php" class="logo">Book<br><span>Mate</span></a>
    <nav>
      <a href="index.php" class="active">Home</a>
      <a href="about.php">About Us</a>
      <a href="hostel.php">Hostel</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="userProfile.php">Profile</a>
        <a href="/hostel/backend/user/includes/logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </nav>
  </header>