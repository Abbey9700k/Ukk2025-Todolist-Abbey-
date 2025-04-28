<?php
include 'koneksi.php';

if(!empty($_POST['username']) && !empty($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = mysqli_query($koneksi, "INSERT INTO user (username,password,level) VALUES ('$username','$password','user')");

    if($result){
        header('location: login.php');
    }
} 