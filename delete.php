<?php
session_start();
require_once('./config/database.php');
require_once('./config/configuration.php');
require_once('./config/auth.php');
require_once('./include/include_function.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $deleteUserSql = "UPDATE `user` SET deleted_at = CURRENT_TIMESTAMP, deleted_by = '$created_by' WHERE id = '$id'";
    $deleteUserQuery = $mysqli->query($deleteUserSql);

    if ($deleteUserQuery) {
        $deleteUser_hobbiesSql = "UPDATE `user_hobbies` SET deleted_at = CURRENT_TIMESTAMP,deleted_by = '$created_by' WHERE user_id = '$id'";
        $deleteUser_hobbiesQuery = $mysqli->query($deleteUser_hobbiesSql);

        $_SESSION['success'] = "Record deleted successfully!";
        $url = $baseUrl . 'index.php/';
        header("Refresh:0;url=$url");
        exit();
    } else {
        $_SESSION['error'] = "Failed to delete record.";
        header("Location: index.php");
        exit();
    }
}
