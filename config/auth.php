<?php
if (!isset($_SESSION['id']) || !$_SESSION['name']) {
    $url = $baseUrl . 'login.php/';
    header("Refresh:0;url=$url");
    exit();
} else {
    $created_by = $_SESSION['id'];
}

if (!isset($_COOKIE['id']) || !$_COOKIE['name']) {
    $url = $baseUrl . 'login.php/';
    header("Refresh:0;url=$url");
    exit();
} else {
    $created_by = $_COOKIE['id'];
}
