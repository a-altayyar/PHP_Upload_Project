<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <?php
    $error_fields = array();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {


        //Validation
        if (!(isset($_POST['add_name']) && !empty($_POST['add_name']))) {
            $error_fields[] = "name";
        }
        if (!(isset($_POST['add_email']) && filter_input(INPUT_POST, 'add_email', FILTER_VALIDATE_EMAIL))) {
            $error_fields[] = "email";
        }
        if (!(isset($_POST['add_psw']) && strlen($_POST['add_psw']) > 5)) {
            $error_fields[] = "Password";
        }


        // Set allowed files Extensions
        $allowed_extensions = array('jpg', 'gif', 'jpeg', 'png');
        //Get files Extension
        $image_extension = strtolower(end(explode('.', $_FILES["avatar"]['name'])));


        // Check fils size 
        if ($_FILES["avatar"]['size'] > 8000) {
            $error_fields[] = "file_size";
        }


        //Connect to DB
        if (!$error_fields) {
            include './DB-CONFIG.php';
            $con = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
            if (!$con) {
                echo mysqli_connect_errno();
                exit;
            }


            //Escape any sepcial characters to avoid SQL Injection
            $name = mysqli_escape_string($con, $_POST['add_name']);
            $em = mysqli_escape_string($con, $_POST['add_email']);
            $password = sha1($_POST['add_psw']);
            $admin = (isset($_POST['add_admin'])) ? 1 : 0;

            $uploads_dir = $_SERVER['DOCUMENT_ROOT'] . '\My_Projects\project_php_form\uploads\\';
            $avatar = '';
            if ($_FILES["avatar"]['error'] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["avatar"]["tmp_name"];
                $avatar = basename($_FILES["avatar"]["name"]);
                move_uploaded_file($tmp_name, "$uploads_dir/$name.$avatar");
            } else {
                echo "File can't be uploaded";
                exit;
            }


            //Insert the data
            $query = "INSERT INTO `listpro` (`id`, `name`, `email`, `password`, `adminn`, `avatar`)
                     VALUES (NULL, '$name', '$em', '$password', '$admin', '$name.$avatar');";
            if (mysqli_query($con, $query)) {
                header("Location: list.php");
                exit;
            } else {
                //echo $query;
                echo mysqli_error($con);
            }

            //Close the connection
            mysqli_close($con);

        }
    }

    ?>

    <h1>Add User</h1>

    <form method="post" enctype="multipart/form-data">

        <label>Name:
            <input type="text" name="add_name" value="<?= (isset($_POST['add_name'])) ? $_POST['add_name'] : '' ?>" />
            <?php if (in_array("name", $error_fields))
                echo "* Please enter your name"; ?>
        </label><br>

        <label>Email:
            <input type="email" name="add_email" value="<?= (isset($_POST['add_email'])) ? $_POST['add_email'] : '' ?>">
            <?php if (in_array("email", $error_fields))
                echo "* Please enter a valid email"; ?>
        </label><br>

        <label>Password :
            <input type="password" name="add_psw" value="<?= (isset($_POST['add_psw'])) ? $_POST['add_psw'] : '' ?>">
            <?php if (in_array("Password", $error_fields))
                echo "* Please enter a password not less then 6 characters"; ?>
        </label><br>

        <label>Admin :
            <input type="checkbox" name="add_admin" <?= (isset($_POST['add_admin'])) ? 'checked' : '' ?>>
        </label><br>

        <label>avatar :
            <input type="file" name="avatar">
            <?php if (in_array("file_size", $error_fields))
                echo "* Please enter a file size not bigger then 6MB"; ?>
        </label><br>

        <input type="submit" value="Add">
    </form>



</body>

</html>