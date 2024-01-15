<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <?php
    session_start();
    if (isset($_SESSION['id'])) {
        echo '<p> Welcome ' . $_SESSION['name'] . ' <a href="./logout.php">Logout</a></p>';
    } else {
        header("Location: login.php");
    }

    //Connect to MySQL
    include './DB-CONFIG.php';
    $con = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if (!$con) {
        echo mysqli_connect_errno();
        exit;
    }

    //Select all users
    $query = "SELECT * FROM userlist";


    //Search by the name or the email
    if (isset($_GET['add_search'])) {
        $search = mysqli_escape_string($con, $_GET['add_search']);
        $query .= " WHERE `userlist`.`name` LIKE '%" . $search . "%' OR `userlist`.`email` LIKE '%" . $search . "%'";
    }

    $result = mysqli_query($con, $query);

    ?>


    <h1>List Users</h1>

    <form method="GET">
        <input type="text" name="add_search" placeholder="Enter {Name} or {Email} to search">
        <input type="submit" value="Search">
    </form>

    <!-- Display a table containq all users -->
    <table border=2>
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Profile Image</th>
                <th>Photos</th>
                <th>Admin</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td>
                        <?= $row['id'] ?>
                    </td>
                    <td>
                        <?= $row['name'] ?>
                    </td>
                    <td>
                        <?= $row['email'] ?>
                    </td>
                    <td>
                        <?php if (!empty($row['avatar'])) { ?>
                            <img src="./img/Photo_Personal/<?= $row['avatar'] ?>" style="width: 100px; height: 100px" />
                        <?php } else { ?>
                            <img src="./img/Photo_Personal/nothing_img.png" style="width: 100px; height: 100px" />
                        <?php } ?>
                    </td>
                    <td>
                        <a href="photos.php?id=<?= $row['id'] ?>">Your Photos</a>
                    </td>
                    <td>
                        <?= ($row['admin']) ? 'Yes' : 'No' ?>
                    </td>
                    <td> <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> | <a
                            href="delete.php?id=<?= $row['id'] ?>">Delete</a>
                    </td>
                </tr>

                <?php
            }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="2" style="text-align: center">
                    <?= mysqli_num_rows($result) ?> User
                </td>
                <td colspan="3" style="text-align: center"><a href="add.php">Add User</a></td>
            </tr>

        </tfoot>
    </table>

    <?php
    mysqli_free_result($result);
    mysqli_close($con);
    ?>


</body>

</html>