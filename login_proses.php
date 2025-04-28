<?php
    include "koneksi.php";
    session_start();
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(!empty($username) && !empty($password)){
        $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username' AND password = '$password'");
        $cek = mysqli_num_rows($query);

        if($cek > 0){
            while($data = mysqli_fetch_array($query)){
                $_SESSION['user'] = $data;
                header("Location:index.php?pesan=login_sukses");
            }
        } else {
            header("Location:login.php?pesan=login_salah");
        }
    } else {
        header("Location:login.php?pesan=login_kosong");
    }
?>