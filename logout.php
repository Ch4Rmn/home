<?php

session_start();
session_unset();
session_destroy();

setcookie("id", "", time() - 3600, "/");
setcookie("name", "", time() - 3600, "/");
setcookie("password", "", time() - 3600, "/");


$url = $baseUrl . 'login.php/';
header("Refresh:0;url=$url");
exit();
