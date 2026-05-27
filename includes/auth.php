<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

function allowRoles($allowed_roles) {
    $role = $_SESSION['role'] ?? '';

    if (!in_array($role, $allowed_roles)) {
        echo "Access denied.";
        exit();
    }
}
?>