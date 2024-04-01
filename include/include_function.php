<?php
require_once('./config/configuration.php');

function generatePass($password, $shaKey)
{

    $generatePass = md5(md5($password) . $shaKey);
    return $generatePass;
}

function checkImageExtension($image_name)
{
    $allow_extension = ['jpg', 'png', 'gif', 'jpeg'];
    $explode = explode('.', $image_name);
    $extension = strtolower(end($explode));

    if (in_array($extension, $allow_extension)) {
        return true;
    } else {
        return false;
    }
}


function isPasswordValid($password)
{
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@!#$])[A-Za-z\d@!#$]+$/';

    if (preg_match($pattern, $password)) {
        return true;
    } else {
        return false;
    }
}
