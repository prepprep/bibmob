<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'vars.php';
require_once 'server_funcs.php';

//@tested
function login($email, $passwd) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("SELECT id FROM user WHERE email = :email "
                . "AND password = :pswd");
        $query->execute(array(':email' => $email, ':pswd' => $passwd));
        $target = $query->fetch()[0];
        $affect_rows = $query->rowCount();
        if ($affect_rows > 0) {
            //session_start(); //The session should be started once logged in
            $_SESSION['id'] = $target;
            $_COOKIE['id'] = $target;
            echo $_SESSION['id'];  //This is the test to found id;
            return true;
        } else {
            echo "<script>alert('Re-enter you login detail')</script>";
            echo "<script>window.location = 'index.php'</script>";
            return false;
        }
    } catch (Exception $ex) {
        echo $ex->getMessage();
        echo 'in login' . PHP_EOL;
        return false;
    }
}

function logout() {
    session_unset();
    session_destroy();
    echo "<script>alert('good bye');</script>";
    echo "<script>window.location = 'index.html'</script>";
}

//@tested
function register($email, $pswd) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("INSERT INTO user (email, password)VALUES"
                . "(:email, :pswd)");
        $query->execute(array(':email' => $email, ':pswd' => $pswd));

        echo 'insert success';
        //get id
        $active_id = get_user_id($email, $pswd);
        $_SESSION['id'] = $active_id;
        setcookie('id', $_SESSION['id']);
        //create tabs
        create_default_lib($active_id);
        create_trash_table($active_id);
        $_SESSION['active_lib'] = get_active_lib($active_id);
        $_SESSION['current_order'] = "ASC";
        $_SESSION['sortby'] = "title";
        echo "<script>window.location = 'bibli.php'</script>";
    } catch (Exception $ex) {
        echo 'register failed' . PHP_EOL;
        $ex->getMessage();
    }
}

function show_libs($id) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("SELECT libName FROM userlib WHERE id = :id ");
        $query->bindParam(':id', $id);
        $query->execute();
        echo "<select id='libs' name='userId' onchange='showref(this.value)'>";
//        echo "<option value=''>Select lib name</option>";

        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            echo "<option value='$result[0]' " . ($_SESSION['active_lib'] == $result[0] ? "selected='selected'" : "") . ">$result[0]</option>";
        }
        echo "</select>";
    } catch (Exception $ex) {
        echo 'get_libs failed' . PHP_EOL;
        $ex->getMessage();
    }
}

function get_all_libs($id) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM userlib WHERE id = :id ";
        $query = $connection->prepare($sql);
        $query->bindParam(':id', $id);
        $query->execute();
        $res = array();
        while ($row = $query->fetch(PDO::FETCH_BOTH)) {
            array_push($res, $row['libName']);
        }
        return $res;
    } catch (Exception $ex) {
        echo 'in get_all_libs' . PHP_EOL;
        $ex->getMessage();
    }
}

function show_all_libs($id) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT libName FROM userlib WHERE id = :id ";
        $query = $connection->prepare($sql);
        $query->bindParam(':id', $id);
        $query->execute();
       
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            echo "<input type='checkbox' name='libs[]' value=" . $result['libName'] . "></input>" . $result['libName'] . "<br/>";
        }
        
    } catch (Exception $ex) {
        echo 'in show_all_libs' . PHP_EOL;
        $ex->getMessage();
    }
}

function get_other_libs($id, $libname) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT libName FROM userlib WHERE id = :id ";
        $query = $connection->prepare($sql);
        $query->bindParam(':id', $id);
        $query->execute();
        echo "<form action='bibli.php' method='post'>";
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            if ($result['libName'] != $libname) {
                echo "<input type='checkbox' name='libs[]' value=" . $result['libName'] . "></input>" . $result['libName'] . "<br/>";
            }
        }
        echo "<input type='hidden' name='action' value='deltab' />";
        echo "<input type='submit' name='submit' value='Delete' /></form>";
    } catch (Exception $ex) {
        echo 'get_libs failed' . PHP_EOL;
        $ex->getMessage();
    }
}

function get_active_lib($id) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $query = $connection->prepare("SELECT libName FROM userlib WHERE id = :id ");
        $query->bindParam(':id', $id);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_BOTH);
        $active_lib = $result[0];
        return $active_lib;
    } catch (Exception $ex) {
        echo 'get_libs failed' . PHP_EOL;
        $ex->getMessage();
    }
}

function get_user_id($email, $pswd) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $query = $connection->prepare("SELECT id FROM user WHERE email = :email "
                . "AND password = :pswd;");
        $query->execute(array(':email' => $email, ':pswd' => $pswd));
        $current_id = $query->fetch()[0];
        return $current_id;
    } catch (Exception $ex) {
        echo 'get_user_id failed' . PHP_EOL;
        $ex->getMessage();
    }
}
