<?php
require_once 'config/database.php';
require_once 'config/functions.php';

requireLogin();

// Redirect based on role
if (isAdmin()) {
    header("Location: admin/dashboard.php");
} else {
    header("Location: user/dashboard.php");
}
exit();
?>
