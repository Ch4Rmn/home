<?php
session_start();
require_once('./config/database.php');
require_once('./config/configuration.php');
require_once('./config/auth.php');
require_once('./include/include_function.php');

if (isset($_POST['Submit'])) {
    $id = $_POST['id'];
    $name = $mysqli->real_escape_string($_POST['name']);
    $age = (int)$_POST['age'];
    $townships = $mysqli->real_escape_string($_POST['townships']);
    $gender = $mysqli->real_escape_string($_POST['gender']);
    $hobbies = isset($_POST['hobby']) ? $_POST['hobby'] : [];
    $url = $baseUrl . 'index.php/';
    $imageFolder = 'image/';

    $user_exist = "SELECT `name` FROM `user` WHERE name='$name' AND id!='$id'";
    $user_exist_query = $mysqli->query($user_exist);

    if ($user_exist_query->num_rows >= 1) {
        echo "User with the same name already exists.";
    } else {
        if ($_FILES['file']['name']) {
            $image_name =  $imageFolder . uniqid() . date("d-m-y")  . basename($_FILES['file']['name']);
            $tmp_name = $_FILES['file']['tmp_name'];

            if (checkImageExtension($image_name)) {
                if (move_uploaded_file($tmp_name, $image_name)) {
                    echo "Image uploaded successfully.";
                    $update_sql = "UPDATE `user` SET name='$name', age='$age', townships='$townships', gender='$gender', image='$image_name' WHERE id=$id";
                }
            }
        } else {
            $update_sql = "UPDATE `user` SET name='$name', age='$age', townships='$townships', gender='$gender' WHERE id=$id";
        }

        $update_query = $mysqli->query($update_sql);

        if ($update_query) {
            $delete_hobbies_sql = "DELETE FROM `user_hobbies` WHERE user_id=$id";
            $mysqli->query($delete_hobbies_sql);

            foreach ($hobbies as $hobby_id) {
                $insert_hobby_sql = "INSERT INTO `user_hobbies` (user_id, hobby_id) VALUES ('$id', '$hobby_id')";
                $mysqli->query($insert_hobby_sql);
            }
            header("Location: $url");
            exit();
        } else {
            echo "Error updating record: " . $mysqli->error;
        }
    }
}
