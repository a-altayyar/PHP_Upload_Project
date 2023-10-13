<?php
//We will use it for storing the signed in user data
session_start();
$error_fields = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Validation
    if (!(isset($_POST['login_email']) && filter_input(INPUT_POST, 'login_email', FILTER_VALIDATE_EMAIL))) {
        $error_fields[] = "email";
    }
    if (!(isset($_POST['login_psw']) && strlen($_POST['login_psw']) > 5)) {
        $error_fields[] = "Password";
    }


    if (!$error_fields) {
        //Connect to MySQL
        include './DB-CONFIG.php';
        $con = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
        if (!$con) {
            echo mysqli_connect_errno();
            exit;
        }

        //Escape any special characters to avoid SQL Injection
        $user_email = mysqli_escape_string($con, trim($_POST['login_email']));
        $user_password = trim($_POST['login_psw']);


        //Select this user
        $sql = "SELECT * FROM userlist WHERE email = '" . $user_email . "' LIMIT 1;";

        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        if (password_verify($_POST['login_psw'], $row['password'])) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            header("Location: list.php");
            exit;
        } else {
            $error = 'Invalid email or password';
        }

        //Close The Connection
        mysqli_free_result($result);
        mysqli_close($con);
    }
}
?>


<head>
    <title>Login</title>
</head>

<body>
    <?php if (isset($error))
        echo $error; ?>

    <form method="post">
        <label for="email">Email</label>
        <input type="email" name="login_email"
            value="<?= (isset($_POST['login_email'])) ? $_POST['login_email'] : '' ?>" />
        <?php if (in_array("email", $error_fields))
            echo "* Please enter a valid email"; ?><br>


        <label for="password">Password</label>
        <input type="Password" name="login_psw" />
        <?php if (in_array("Password", $error_fields))
            echo "* Please enter a password not less then 6 characters"; ?><br><br>

        <input type="submit" value="Login">
    </form>
</body>