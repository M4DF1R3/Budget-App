<?php
// Connection to the database
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "budget-app";

if(!$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname))
{
    die("failed to connect!");
}
?>