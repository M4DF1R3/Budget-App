<?php
session_start();
include("connection.php");
include("functions.php");
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        //something was posted
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (!empty($username) && !empty($password) && !is_numeric($username)) {
            //save to database
            $user_id = random_num(20);
            $query = "insert into users (user_id, username, password) values ('$user_id', '$username', '$password')";
            mysqli_query($con, $query);
            header("Location: login.php");
            die;
        } else {
            echo "Please enter some valid information!";
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Signup</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body>
    <h1>Signup</h1>
    <div id="box">
        <form method="post">

            <input id="text" type="text" name="username" placeholder="Username"><br><br>
            <input id="text" type="password" name="password" placeholder="Password"><br><br>
            <button id="button" type="submit" name="submit">Login</button><br><br>
            <a href="login.php">Click to Login</a>
        </form>


        <!-- Load JavaScript -->
        <script src="js/users.js"></script>
</body>

</html>

</body>