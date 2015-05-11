<?php

require_once 'webpage_functions.php';
session_start();


if (!empty($_POST['status'])) {
    $status = $_POST['status'];
} else {
    echo 'please state what do you want to do.';
}

switch ($status) {
    case 'login':
        if (!empty($_POST['email']) && !empty($_POST['passwd'])) {
            $email = trim($_POST['email']);
            $passwd = trim($_POST['passwd']);
        }
        if (login($email, $passwd)) {
            setcookie('id', $_SESSION['id']);
            $_SESSION['active_lib'] = get_active_lib($_SESSION['id']);
            $_SESSION['current_order'] = "ASC";
            $_SESSION['sortby'] = "title";
            echo 'Welcome to ' . PHP_EOL;
            echo 'your id is:  ' . $_SESSION['id'];
            echo 'your cookie id is:  ' . $_COOKIE['id'];
            echo "<script>window.location = 'bibli.html'</script>";
        }
        break;

    case 'logout':
        logout();
        break;

    case 'register':
        if (!empty($_POST['email']) && !empty($_POST['passwd'])) {
            if (is_unique_email($_POST['email'])) {
                $email = trim($_POST['email']);
                $passwd = trim($_POST['passwd']);
                register($email, $passwd);
                echo 'register success';
            }  
        }
        break;

    default:
        echo 'no such status';
        break;
}