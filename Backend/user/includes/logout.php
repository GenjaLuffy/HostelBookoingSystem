<?php
session_start();

session_unset();
session_destroy();

header("Location: /hostel/backend/user/index.php");

exit;
?>
