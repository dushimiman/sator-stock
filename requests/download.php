<?php
session_start();

if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $filepath = dirname(__FILE__) . '/' . $file;

    if (file_exists($filepath)) {
        // Send headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        $_SESSION['message'] = "File not found.";
    }