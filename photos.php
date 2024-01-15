<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<?php
session_start();
if (isset($_SESSION['id'])) {
    echo '<p> Welcome ' . $_SESSION['name'] . ' <a href="./logout.php">Logout</a></p>';
} else {
    header("Location: login.php");
}


//Connect to DB
include './DB-CONFIG.php';
$con = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
if (!$con) {
    echo mysqli_connect_errno();
    exit;
}

//select the user
//edit.php?id=1 => $_GET['id']
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$select = "SELECT * FROM userlist WHERE `userlist`.`id` = " . $id . " LIMIT 1";
$result = mysqli_query($con, $select);
$row = mysqli_fetch_assoc($result);

?>

<body>

    <strong>Your Photos</strong>
    <table border=3>
        <tr>
            <?php
            $explode_file = explode(',', $row['photos']);
            foreach ($explode_file as $file):
                ?>
                <td>
                    <?php if (!empty($file)) { ?>
                        <img src="./img/Photo_Library/<?= $file ?>" style="width: 100px; height: 100px" />
                    <?php } else { ?>
                        <img src="./img/Photo_Library/no-results.png" style="width: 100px; height: 100px" />
                    <?php } ?>
                </td>
                <?php
            endforeach;
            ?>
        </tr>
    </table><br>

    <div>
        <a href="./photo_MultipleUpload?id=<?= $id ?>">Add / Edit Photos </a> |
        <a href="./list.php"> Back</a>
    </div>


    <?php
    mysqli_free_result($result);
    mysqli_close($con);
    ?>
</body>