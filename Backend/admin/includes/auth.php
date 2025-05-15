<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['role'])) {
    echo "<script>alert('Please log in first.'); window.location.href = '../user/login.php';</script>";
    exit;
}

// Optional: If restricting to admin/superadmin only
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin') {
    echo "<script>alert('Access Denied'); window.location.href = '../user/login.php';</script>";
    exit;
}
?>
