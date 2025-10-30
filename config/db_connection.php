<?php
    $username = "root";
    $pass = "2002";
    $host = "localhost";
    $db = "skillbridge";

   global $conn;
   $conn= new mysqli($host,$username,$pass,$db);


    if($conn->connect_error){
        die("Connection error".mysqli_error($conn));
    }

?>