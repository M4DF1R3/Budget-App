<?php
session_start();
    $_SESSION;
    include("connection.php");
    include("functions.php");
    $user_data = check_login($con);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Budget App</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body>
    <h1>BUDGET APP</h1>
    <h2>Hello, <?php echo $user_data['username']; ?></h1>
    <a href="logout.php">Logout</a>

    <p class="enter-input">Enter Your Expense Amount: <input id="expense-value" type="number" class="user-input-expense"></p>
    

    <!-- Load JavaScript -->
    <script src="js/users.js"></script>
</body>

</html>

</body>