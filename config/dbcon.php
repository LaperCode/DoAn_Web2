<?php
    $host="localhost";
    $username= "root";
    $password="";
    $database="atshop_db";

    $conn=mysqli_connect($host, $username, $password, $database, 3306);
    mysqli_set_charset($conn,'utf8');
    //check database
    if (!$conn) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }
    
?>