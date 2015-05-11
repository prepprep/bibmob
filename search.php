<?php

session_start();
require_once 'vars.php';
require_once 'webpage_functions.php';
require_once 'server_funcs.php';

if (isset($_POST['submit']) || !empty($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
//    print_r($keyword);
//    $keyword = 'b';
//    print_r($libs);
    $libname = $_SESSION['active_lib'];
//    $libname = 'lib1';
    echo "<table>
        <tr>
        <th>Library</th>
        <th>Title</th>
        <th>Author</th>
        <th>Year</th>
        <th>Time</th>
        <th>PDF</th>
        </tr>
        ";
    
    search($libname, $keyword);
//    echo json_encode($sres);
    echo "</table>";
}  else {
    echo 'wrong=' . $_GET['keyword'];
}
