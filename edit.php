<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <?php
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

    $error_fields = array();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //Validation
        if (!(isset($_POST['edit_name']) && !empty($_POST['edit_name']))) {
            $error_fields[] = "name";
        }
        if (!(isset($_POST['edit_email']) && filter_input(INPUT_POST, 'edit_email', FILTER_VALIDATE_EMAIL))) {
            $error_fields[] = "email";
        }
        if (!(isset($_POST['old_psw']) && strlen($_POST['old_psw']) > 5)) {
            $error_fields[] = "old_Password";
        }
        if (!(isset($_POST['new_psw']) && strlen($_POST['new_psw']) > 5)) {
            $error_fields[] = "new_Password";
        }
        //this if condition to ensure that password matches in the DataBase. if it matches, the password change is complete. 
        if (password_verify($_POST['old_psw'], $row['password'])) {
            $user_hashed_password = password_hash(trim($_POST['new_psw']), PASSWORD_DEFAULT);
        } else {
            $error_fields[] = "Password_in_correct";
        }

        if (!$error_fields) {


            $idd = filter_input(INPUT_POST, 'her', FILTER_SANITIZE_NUMBER_INT);
            //Escape any sepcial characters to avoid SQL Injection
            // trim to deal with the unneeded white lift and right spaces 
            $user_name = mysqli_escape_string($con, trim($_POST['edit_name']));
            $user_email = mysqli_escape_string($con, trim($_POST['edit_email']));
            $admin = (isset($_POST['edit_admin'])) ? 1 : 0;

            //Update the data
            $query = "UPDATE `userlist` SET `id` = '$id', `name` = '$user_name', `email` = '$user_email', `password` = '$user_hashed_password', `admin` = '$admin' WHERE `userlist`.`id` = '$idd'";
            if (mysqli_query($con, $query)) {
                header("Location: list.php");
                exit;
            } else {
                //echo $query;
                echo mysqli_error($con);
            }

        }
    }

    //Close the connection
    mysqli_free_result($result);
    mysqli_close($con);
    ?>


    <h1>Edit User</h1>

    <form method="post">
        <input type="hidden" name="her" value="<?= (isset($row['id'])) ? $row['id'] : '' ?>">

        <label>Name:
            <input type="text" name="edit_name" value="<?= (isset($row['name'])) ? $row['name'] : '' ?>" />
            <?php if (in_array("name", $error_fields))
                echo "* Please enter your name"; ?>
        </label><br>

        <label>Email:
            <input type="email" name="edit_email" value="<?= (isset($row['email'])) ? $row['email'] : '' ?>">
            <?php if (in_array("email", $error_fields))
                echo "* Please enter a valid email"; ?>
        </label><br>

        <label>Old Password :
            <input type="text" name="old_psw">
            <?php if (in_array("old_Password", $error_fields)) {
                echo "* Please enter a password not less then 6 characters";
            } elseif (in_array("Password_in_correct", $error_fields)) {
                echo "* a password is Password in-correct";
            } ?>
        </label><br>
        <label>New Password :
            <input type="text" name="new_psw">
            <?php if (in_array("new_Password", $error_fields))
                echo "* Please enter a password not less then 6 characters";
            ?>
        </label><br>

        <label>Admin :
            <input type="checkbox" name="edit_admin" <?= ($row['admin']) ? 'checked' : '' ?>>
        </label><br>


        <input type="submit" value="edit">
    </form>



</body>

</html>