<?php
session_start();
include("config.php");
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //something was posted
    $email = $_POST['email'];
    $password = $_POST['password'];
    if (!empty($email) && !empty($password) && !is_numeric($email)) {
        //read from database
        $query = "select * from users where email = '$email' limit 1";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows(($result)) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            if ($user_data['password'] === $password) {
                $_SESSION['user_id'] = $user_data['user_id'];
                header("Location: index.php");
                die;
            }
        }
        echo "Wrong email or password!";
    } else {
        echo "Please enter your email and password!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body>
    <h1>Login</h1>
    <div id="box">
        <form method="post">

            <input id="text" type="text" name="email" placeholder="Email"><br><br>
            <input id="text" type="password" name="password" placeholder="Password"><br><br>
            <button id="button" type="submit" name="submit">Login</button><br><br>
            <a href="signup.php">Click to Signup</a>
        </form>


        <!-- Load JavaScript -->
        <script src="js/users.js"></script>
</body>

</html>
