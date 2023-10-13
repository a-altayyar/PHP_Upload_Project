<?php
session_start();
if (isset($_SESSION['id'])) {
    echo '<p> Welcome ' . $_SESSION['name'] . ' <a href="./logout.php">Logout</a></p>';
} else {
    header("Location: login.php");
}


include './DB-CONFIG.php';
$con = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
if (!$con) {
    echo mysqli_connect_errno();
    exit;
}

//Select the user $_GET['id']
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$query = "DELETE FROM userlist WHERE `userlist`.`id` = " . $id . " LIMIT 1;";
if (mysqli_query($con, $query)) {
    header("Location: list.php");
    exit;
} else {
    //echo $query;
    echo mysqli_error($con);
}

mysqli_close($con);
?>