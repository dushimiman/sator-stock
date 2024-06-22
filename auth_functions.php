<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function checkRole($required_role) {
    session_start();
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $required_role) {
        header('Location: unauthorized.php');
        exit();
    }
}
?>

