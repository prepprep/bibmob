<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'vars.php';

function get_sorted_refs($libname, $sortby, $order) {
    $sql = "SELECT * FROM $libname";

    //be careful, there should be a space before ORDER
    switch ($sortby) {
        case 'title':
            $sort = " ORDER BY title ";
            break;
        case 'author':
            $sort = " ORDER BY author ";
            break;
        case 'year':
            $sort = " ORDER BY author ";
            break;
        case 'addedAt':
            $sort = " ORDER BY author ";
            break;
        case 'pdf':
            $sort = " ORDER BY author ";
            break;
        default:
            $sort = " ORDER BY title ";
            break;
    }

    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = $sql . $sort . $order;
        $query = $connection->prepare($sql);
        $query->execute();

        echo "
            <table>
        <tr>
        <th>Library</th>
        <th>Title</th>
        <th>Author</th>
        <th>Year</th>
        <th>Add At</th>
        <th>PDF</th>
        </tr>
        ";
        while ($row = $query->fetch()) {
            echo '<tr>';
            echo "<td><input class='refs' name='checkbox[]' type='checkbox' value= " . $row['libid'] . " /></td>";
            echo '<td>' . $row['title'] . '</td>';
            echo '<td>' . $row['author'] . '</td>';
            echo '<td>' . $row['year'] . '</td>';
            echo '<td>' . $row['addedAt'] . '</td>';
            echo '<td>' . $row['pdf'] . '</td>';
            echo '</tr>';
        }
        echo "
             <input type='hidden' name='action' value='deleteref'/>
            <input type='hidden' name='delname' value=" . $_SESSION['active_lib'] . "></input>
                                </table>
        ";
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }
}

function get_trash($id) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $libname = 'trash' . $id;
        $sql = "SELECT * FROM $libname";
        $query = $connection->prepare($sql);
        $query->execute();

        echo "<table>
        <tr>
        <th>Trash</th>
        <th>title</th>
        <th>author</th>
        <th>year</th>
        <th>added at</th>
        <th>pdf</th>
        </tr>
        <form action='deleteTrash.php' method='post'>";
        while ($row = $query->fetch()) {
            echo '<tr>';
            echo "<td><input name='trash[]' type='checkbox' value= " . $row['trashlibid'] . " /></td>";
            echo '<td>' . $row['title'] . '</td>';
            echo '<td>' . $row['author'] . '</td>';
            echo '<td>' . $row['year'] . '</td>';
            echo '<td>' . $row['addedAt'] . '</td>';
            echo '<td>' . $row['pdf'] . '</td>';
            echo '</tr>';
        }
        echo "
        </form>
        </table>";

//                    <input type='hidden' name='action' value='deletetrash'/>
//            <input type='submit' name='submit' value='Delete Refs'/>
    } catch (Exception $ex) {
        echo 'get_trash failed' . PHP_EOL;
        $ex->getMessage();
    }
}

function get_refArray($libname, $id) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("SELECT * FROM $libname WHERE libid=:libid;");
        $query->bindParam(':libid', $id);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        echo 'get_ref failed' . PHP_EOL;
        $ex->getMessage();
    }
}

function create_default_lib($id) {
    $libname = 'lib' . strval($id);
    insert_userlib($id, $libname);
    create_table($libname);
}

function create_new_lib($id, $libname) {
    insert_userlib($id, $libname);
    create_table($libname);
}

function insert_userlib($id, $libname) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("INSERT INTO userlib (id,libName) "
                . "VALUES (:id, :libName);");
        $query->execute(array(':id' => $id, ':libName' => $libname));
    } catch (Exception $ex) {
        echo 'in insert_ Userlib' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function del_from_userlib($id, $tabname) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM userlib WHERE id = :id AND libName = :tableName ";
        $query = $connection->prepare($sql);
        $query->execute(array(':id' => $id, ':tableName' => $tabname));
    } catch (Exception $ex) {
        echo 'in insert_ Userlib' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function insert_trashlib($id, $tabname) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("INSERT INTO trashlib (id,libName,trashlibName) "
                . "VALUES (:id, :libName, :trashlibName);");
        $query->execute(array(':id' => $id, ':libName' => $tabname, ':trashlibName' => $tabname));
    } catch (Exception $ex) {
        echo 'in insert_ trashlib' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function create_table($libname) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE $libname (
        libid int(10) NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
                author VARCHAR(255) NOT NULL,
                year VARCHAR(25) NOT NULL,
                addedAt TIMESTAMP,
                pdf VARCHAR(255),
                PRIMARY KEY(libid)
            );";
        $query = $connection->prepare($sql);
        $query->execute();
    } catch (Exception $ex) {
        echo 'in create_table' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function create_trash_table($id) {
    $trashname = 'trash' . $id;
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE $trashname (
                trashlibid int(10) NOT NULL AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                author VARCHAR(255) NOT NULL,
                year VARCHAR(25)  NOT NULL,
                addedAt TIMESTAMP,
                pdf VARCHAR(255),
                PRIMARY KEY(trashlibid) 
                );";
        $query = $connection->prepare($sql);
        $query->execute();
    } catch (Exception $ex) {
        echo 'in create_trash_table' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function is_unique_email($email) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("SELECT id FROM user WHERE email = :email ");
        $query->bindParam(':email', $email);
        $query->execute();
        $affect_rows = $query->rowCount();
        if ($affect_rows > 0) {
            echo "<script>alert('email already in use');</script>";
            echo "<script>window.location = 'register.html'</script>";
            return false;
        }
        return true;
    } catch (Exception $ex) {
        echo 'in is_already_exist_libname' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function is_already_exist_libname($libname) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("SELECT id FROM userlib WHERE libName = :libname ");
        $query->bindParam(':libname', $libname);
        $query->execute();
        $affect_rows = $query->rowCount();
        if ($affect_rows > 0) {
            return true;
        }
        return false;
    } catch (Exception $ex) {
        echo 'in is_already_exist_libname' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function create_unfiled_table() {
    //
}

function insertRef($libname, $title, $author, $year, $pdf) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("INSERT INTO $libname (title,author,year,pdf) "
                . "VALUES (:title,:author,:year,:pdf);");
        $query->execute(array(
            ':title' => $title,
            ':author' => $author,
            ':year' => $year,
            ':pdf' => $pdf));
    } catch (Exception $ex) {
        echo 'in insertRef' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function insertRef_trash($trash, $libname, $title, $author, $year, $pdf) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("INSERT INTO $trash (libName,title,author,year,pdf) "
                . "VALUES (:libname,:title,:author,:year,:pdf);");
        $query->execute(array(
            ':libname'=>$libname,
            ':title' => $title,
            ':author' => $author,
            ':year' => $year,
            ':pdf' => $pdf));
    } catch (Exception $ex) {
        echo 'in insertRef_trash' . PHP_EOL;
        echo $ex->getMessage();
    }
}

//this function should be used in trash, not 
function delete_ref($libname, $libid) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("DELETE FROM $libname WHERE libid = :libid ");
        $query->bindParam(':libid', $libid);
        $query->execute();
    } catch (Exception $ex) {
        echo 'in delete_ref' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function delete_trash($id, $trashlibid) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $libname = 'trash' . $id;
        $sql = "DELETE FROM $libname WHERE trashlibid = :trashlibid ";
        $query = $connection->prepare($sql);
        $query->bindParam(':trashlibid', $trashlibid);
        $query->execute();
    } catch (Exception $ex) {
        echo 'in delete_trash' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function cleanup_trash($id) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $libname = 'trash' . $id;
        $sql = "TRUNCATE  TABLE $libname ";
        $query = $connection->prepare($sql);
        $query->execute();
    } catch (Exception $ex) {
        echo 'in cleanup_trash' . PHP_EOL;
        echo $ex->getMessage();
    }
}

//a user could have no more than 4 libs at the same time including the trash.
function allow_more_libs($id) {

    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT COUNT(*) FROM userlib WHERE id=:id ";
        $query = $connection->prepare($sql);
        $query->bindParam(':id', $id);
        $query->execute();
        $num = $query->fetch();
        if ($num[0] < 4) {
            return true;
        }
        return false;
    } catch (Exception $ex) {
        echo 'in is_max_libs' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function update_ref($libname, $refid, $title, $author, $year, $pdf) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = <<<EOSQL
                UPDATE $libname SET 
                    title=:title, 
                    author=:author, 
                    year=:year, 
                    pdf=:pdf 
                    WHERE libid = :refid;
EOSQL;

        $query = $connection->prepare($sql);

        $query->execute(array(
            ':title' => $title,
            ':author' => $author,
            ':year' => $year,
            ':pdf' => $pdf,
            ':refid' => $refid
        ));
    } catch (Exception $ex) {
        echo 'in update_ref' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function rename_table($current, $to) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "RENAME TABLE $current TO $to;";
        $query = $connection->prepare($sql);
        $query->execute();
    } catch (Exception $ex) {
        echo 'in rename_table' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function update_libname($id, $old, $new) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_insert = "INSERT INTO userlib (id,libName) VALUES(:id, :libname)";
        $sql_delete = "DELETE FROM userlib WHERE libName = :libname;";
        $query_insert = $connection->prepare($sql_insert);
        $query_insert->execute(array(':id' => $id, ':libname' => $new));
        $query_delete = $connection->prepare($sql_delete);
        $query_delete->execute(array(':libname' => $old));
    } catch (Exception $ex) {
        echo 'in update_libname' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function shareWith($owner, $withid, $libname) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO share(owner, shareWith, libName) VALUES 
                (:owner, :shareWith, :libName);";
        $query = $connection->prepare($sql);
        $query->execute(array(':owner' => $owner, ':shareWith' => $withid, ':libName' => $libname));
    } catch (Exception $ex) {
        echo 'in shareWith' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function get_id($email) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("SELECT id FROM user WHERE email = :email ");
        $query->bindParam(':email', $email);
        $query->execute();
        $res = $query->fetch();
        return $res['id'];
    } catch (Exception $ex) {
        echo 'in get_id' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function view_shared_lib($id) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM share WHERE owner = :owner ORDER BY libName;";
        $query = $connection->prepare($sql);
        $query->bindParam(':owner', $id);
        $query->execute();

        echo "<table>";
        echo "
            <tr>
            <th>Library</th>
            <th>Share With</th>
            <th>--</th>
            </tr>
        ";
        while ($result = $query->fetch(PDO::FETCH_BOTH)) {
            echo "<form action='unshare_server.php' method='post'>";
            echo "<tr><td><input type='checkbox' name='lib' value=" . $result['libName'] . "></input>" . $result['libName'] . "</td>";
            echo "<td><input type='hidden' name='withid' value=" . $result['shareWith'] . "></input>" . id_get_email($result['shareWith']) . "</td>";
            echo "<td><input type='submit' name='submit' value='Unshare' /></form></tr></td>";
        }
        echo "</table>";
        echo "<a href='bibli.php'>Go Back</a>";
    } catch (Exception $ex) {
        echo 'in view_shared_lib' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function id_get_email($id) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $connection->prepare("SELECT email FROM user WHERE id = :id ");
        $query->bindParam(':id', $id);
        $query->execute();
        $res = $query->fetch();
        return $res['email'];
    } catch (Exception $ex) {
        echo 'in get_id' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function delete_shared_lib($owner, $withid, $libname) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM share WHERE owner = :owner AND shareWith = :withid AND libName = :libname;
                ";
        $query = $connection->prepare($sql);
        $query->execute(array(':owner' => $owner, ':withid' => $withid, ':libname' => $libname));
    } catch (Exception $ex) {
        echo 'in delete_shared_lib' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function is_email_exist($email) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT id FROM user WHERE email = :email";
        $query = $connection->prepare($sql);
        $query->bindParam(':email', $email);
        $query->execute();
        if ($query->rowCount() > 0) {
            return true;
        }
        return false;
    } catch (Exception $ex) {
        echo 'in is_email_exist' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function remove_table($id, $tabname) {
    insert_trashlib($id, $tabname);
    del_from_userlib($id, $tabname);
}

function drop_trash_table($id, $tabname) {
    del_from_userlib($id, $tabname);
    drop_tab($tabname);
}

function drop_tab($tabname) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DROP TABLE $tabname ";
        $query = $connection->prepare($sql);
        $query->execute();
    } catch (Exception $ex) {
        echo 'in drop_trash_table' . PHP_EOL;
        echo $ex->getMessage();
    }
}

function search($libname, $keyword) {
    try {
        $connection = new PDO(db_dsn, db_username, db_passwd);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT  * FROM $libname WHERE title LIKE '%" . $keyword . "%' OR author LIKE '%" . $keyword . "%'
            OR year LIKE '%" . $keyword . "%' OR addedAt LIKE '%" . $keyword . "%' ";
        $query_libs = $connection->prepare($sql);
        $query_libs->execute();

        while ($row = $query_libs->fetch()) {
            echo '<tr>';
            echo "<td>$libname</td>";
//            echo "<td><input name='srh[]' type='checkbox' value= " . $row['libid'] . " /></td>";
            echo '<td>' . $row['title'] . '</td>';
            echo '<td>' . $row['author'] . '</td>';
            echo '<td>' . $row['year'] . '</td>';
            echo '<td>' . $row['addedAt'] . '</td>';
            echo '<td>' . $row['pdf'] . '</td>';
            echo '</tr>';
        }
    } catch (Exception $ex) {
        echo 'in search' . PHP_EOL;
        echo $ex->getMessage();
    }
}

//how could i avoid two uses have the same table name?
//dont let the user use the same table name.