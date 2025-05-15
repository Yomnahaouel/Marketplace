<?php
$servername ="localhost";
$username = "root";
$password = "";
$database = "marketdb";

$conn = mysqli_connect($servername , $username , $password , $database);

if(!$conn){
    echo " Database connection failed !! ";
}
?>