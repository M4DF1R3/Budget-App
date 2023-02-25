<?php
function check_login($con) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        echo "Error: user_id is not set in session";
    }

    // Redirect to login
    header("Location: login.php");
    die;
}

function random_num($length) {
    $text = "";
    if ($length < 5) {
        $length = 5;
    }
    $len = rand(4, $length);

    for ($i=0; $i < $len; $i++) { 
        $text .= rand(0, 9);
    }

    return $text;
}
?>