<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if user is logged in
if (empty($_SESSION['role'])) {
    echo "<script>alert('Please log in first.'); window.location.href = '../user/login.php';</script>";
    exit;
}

// Restrict access to admin or superadmin only
$allowed_roles = ['admin', 'superadmin'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    echo "<script>alert('Access Denied. You do not have permission to access this page.'); window.location.href = '../user/login.php';</script>";
    exit;
}
?>
