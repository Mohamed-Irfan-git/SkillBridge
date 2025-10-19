<?php
    $username = "root";
    $pass = "Irfan#@123";
    $host = "localhost";
    $db = "skillbridge";

    $conn = new mysqli($host,$username,$pass,$db);


    if($conn->connect_error){
        die("Connection error".mysqli_error($conn));
    }
   
?>