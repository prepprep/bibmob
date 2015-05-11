<?php

session_start();

require_once 'webpage_functions.php';

$res = get_all_refs($_SESSION['active_lib'], $_SESSION['current_order']);

echo json_encode($res);

function get_all_refs($libname, $order) {
    $sql = "SELECT * FROM $libname";
    $sort = " ORDER BY title ";

    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = $sql . $sort . $order;
        $query = $connection->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }
}
