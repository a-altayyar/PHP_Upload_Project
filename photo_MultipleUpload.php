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


if ($_SERVER['REQUEST_METHOD'] == 'POST'):

    // this if condition to ensure that you Back without uploading photos.
    if (isset($_POST["Back"])) {
        header("Location: photos.php?id=$id");
        exit;
    }

    // Setting Errors Array
    $errors = array();

    // **Setting Database File Name
    $all_file = array();

    $upload_file = $_FILES['my_work'];

    // Get Info From The Form
    $file_name = $upload_file['name'];
    $file_type = $upload_file['type'];
    $file_tmp = $upload_file['tmp_name'];
    $file_size = $upload_file['size'];
    $file_error = $upload_file['error'];

    // Set Allowed Files Extensions
    $allowed_extensions = array('jpg', 'gif', 'jpeg', 'png');

    // Check If File Is Uploaded 
    if ($file_error[0] == 4): // No File Uploaded

        echo '<div>No File Uploaded</div>';

    else: // There Files Uploaded 

        $files_count = count($file_name);

        for ($i = 0; $i < $files_count; $i++) {

            // Setting Errors Array
            $errors = array();

            // Get file Extension
            $explode_file = explode('.', $file_name[$i]);
            $file_extension[$i] = strtolower(end($explode_file));

            // Get Random Name For File
            $file_random[$i] = rand(0, 10000000000) . '.' . $file_extension[$i];

            // Check File Size
            if ($file_size[$i] > 80000):

                $errors[] = "<div>File Can't Be More Then X </div>";

            endif;

            if (!in_array($file_extension[$i], $allowed_extensions)):

                $errors[] = "<div>File Is Not Valid </div>";

            endif;

            // Check If Has No Errors
            if (empty($errors)):

                // Move The Files
                $uploads_dir = $_SERVER['DOCUMENT_ROOT'] . '\My_Projects\project_php_upload\img\Photo_Library\\';
                move_uploaded_file($file_tmp[$i], "$uploads_dir/$file_name[$i]" . "$file_random[$i]");

                // Success Massage
                echo '<div style="background-color: #EEE; padding: 10px; margin-bottom: 20px">';
                echo '<div>File Number: ' . ($i + 1) . '</div>';
                echo '<div>File Name: ' . $file_name[$i] . '</div> Uploaded';
                echo '</div>';

                // **if You Want To Upload These Accepted Files
                $all_file[] = $file_name[$i] . $file_random[$i];

            else:

                echo '<div style="background-color: #EEE; padding: 10px; margin-bottom: 20px">';
                echo 'File Number: ' . ($i + 1) . '<br>';
                echo 'File Name: ' . $file_name[$i];
                foreach ($errors as $error):

                    echo $error;

                endforeach;
                echo '</div>';

            endif;
        }

    endif;

    // This if condition, if there are any images before, they will be merge with new images.
    if (!empty($row['photos'])) {

        $new_photo = explode(',', $row['photos']);

        for ($i = 0, $j = count($new_photo) + 1; $i < count($all_file); $i++, $j++)
            $new_photo[$j] = $all_file[$i];

    }

    if (isset($new_photo))
        $file_field = implode(',', $new_photo);
    else
        $file_field = implode(',', $all_file);

    $query = "UPDATE `userlist` SET `photos` = '$file_field' WHERE `userlist`.`id` = '$id'";
    if (mysqli_query($con, $query)) {
        header("Location: photos.php?id=$id");
        exit;
    } else {
        //echo $query;
        echo mysqli_error($con);
    }

endif;

?>

<form method="post" enctype="multipart/form-data">

    <?php
    if (!empty($row['photos'])) {
        ?>

        <strong>Your Photos</strong>
        <table border=3>

            <thead>
                <tr>
                    <th>
                        #
                    </th>
                    <th>
                        Photo
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php
                $explode_file = explode(',', $row['photos']);
                $count_photo = count($explode_file);
                for ($i = 0; $i < $count_photo; $i++) {
                    ?>
                    <tr>
                        <td>
                            <?= $i + 1 ?>
                        </td>
                        <td>
                            <?php if (!empty($explode_file[$i])) { ?>
                                <img src="./img/Photo_Library/<?= $explode_file[$i] ?>" style="width: 100px; height: 100px" />
                            <?php } else { ?>
                                <img src="./img/Photo_Library/no-results.png" style="width: 100px; height: 100px" />
                            <?php } ?>
                            <?php
                            ?>
                        </td>
                        <td>
                            <a href="photo_delete.php?name=<?= $explode_file[$i] ?>&id=<?= $id ?>&index=<?= $i ?>">Delete</a>
                        </td>

                    </tr>
                    <?php
                }
                ?>
            </tbody>

        </table><br>

        <?php
    } else {
        ?>
        <div style="background-color: #EEE; padding: 10px; margin-bottom: 20px">*Your photo library is empty</div>
        <?php
    }
    ?>

    <input type="file" name="my_work[]" multiple="multiple"><br><br>
    <input type="submit" name="mySubmit" value="Upload">
    <input type="submit" name="Back" value="Back">

    <?php
    //Close the connection
    mysqli_free_result($result);
    mysqli_close($con);
    ?>
</form>