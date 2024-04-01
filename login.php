<?php
session_start();
require_once('./config/database.php');
require_once('./config/configuration.php');
require_once('./include/include_function.php');

if (isset($_POST['login'])) {
    $name = $mysqli->real_escape_string($_POST['name']);
    $password = $_POST['password'];

    $loginSql = "SELECT `id`, `name`, `password` FROM `user` WHERE name = '$name' AND password = '$password'";
    $loginQuery = $mysqli->query($loginSql);

    if ($loginQuery->num_rows > 0) {
        $user = $loginQuery->fetch_assoc();

        if (isset($_POST['remember']) || ($_POST['remember']) == 1) {
            $cookieName = "id";
            $cookieValue = $user['id'];
            setcookie($cookieName, $cookieValue, time() + (86400 * 30), "/");
            $cookieName = "name";
            $cookieValue = $user['name'];
            setcookie($cookieName, $cookieValue, time() + (86400 * 30), "/");
            // $cookieName = "password";
            // $cookieValue = $user['password'];
            // setcookie($cookieName, $cookieValue, time() + (86400 * 30), "/");
        } else {
            $_SESSION['name'] = $user['name'];
            $_SESSION['id'] = $user['id'];
            $_SESSION['password'] = $user['password'];
            $_SESSION['success'] = "You are logged in";
        }

        $url = $baseUrl . 'index.php/';
        header("Refresh:0;url=$url");
        exit();
    } else {
        $_SESSION['error'] = "<div style='color:red;'>Wrong Credential!</div>";
        $url = $baseUrl . 'login.php/';
        header("Refresh:0;url=$url");
        exit();
    }
}
?>
<html>

<head>
    <title></title>
    <style>
        .container {
            width: 50%;
            height: auto;
            padding: 50px;
            margin: 0 auto;
            border: 1px solid black;
        }
    </style>
</head>

<body class="container">
    <h3 align="center"><u>Login Form</u></h3>
    <br /><br /><br /><br />

    <?php if (isset($_SESSION['error'])) {
        echo $_SESSION['error'];
    } ?>


    <form action="<?php $baseUrl ?>login.php" method="post" class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-2" for="email">name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="name" placeholder="Please enter the name">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="phone">Password</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" name="password" placeholder="Please enter the password">
            </div>
        </div>
        <br /><br />
        <input type="checkbox" name="remember" value="1">Remember

        <input type="submit" name="login" value="Submit">
    </form>
    </div>
</body>

</html>