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

// the "index" is the index of the image in the DB that will be deleted 
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$index = filter_input(INPUT_GET, 'index', FILTER_SANITIZE_NUMBER_INT);
$photo_name = $_GET['name'];

$select = "SELECT * FROM userlist WHERE `userlist`.`id` = " . $id . " LIMIT 1";
$result = mysqli_query($con, $select);
$row = mysqli_fetch_assoc($result);


$photo_path = "img/Photo_Library/" . $photo_name;

// Make sure the file exists, then delete it from folders and then from DB.
if (file_exists($photo_path)) {

    /*  delete it from folders.  */
    unlink($photo_path);

    /*  Create a new Array excluding the image sent to us by GET['index'] and then upload the new Array to DB.  */
    $explode_file = explode(',', $row['photos']);
    $new_photo = array();

    for ($i = 0; $i < count($explode_file); $i++) {
        if ($i == $index)
            continue;

        $new_photo[$i] = $explode_file[$i];
    }

    $file_field = implode(',', $new_photo);

    $query = "UPDATE `userlist` SET `photos` = '$file_field' WHERE `userlist`.`id` = '$id'";
    if (mysqli_query($con, $query)) {
        header("Location: photo_MultipleUpload.php?id=$id");
        exit;
    } else {
        echo mysqli_error($con);
    }

} else {
    header("Location: photos.php?id=$id");
    exit;
}

mysqli_close($con);
?>