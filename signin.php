<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Daftar Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .login-box {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="login-box">

            <?php
            if (!empty($_GET['pesan'])) {
                switch ($_GET['pesan']) {
                    case 'register_kosong':
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Maaf!</strong> Username atau Password Kosong
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
                        break;
                    case 'register_gagal':
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Gagal!</strong> Registrasi Gagal
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
                        break;
                }
            }
            ?>

            <form action="signin_proses.php" method="post">
                <h1 class="text-center mb-4">Daftar</h1>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" id="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Buat Akun</button>
            </form>

            <a href="login.php" class="btn btn-link w-100 text-center d-block mt-3">Kembali ke Login</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
</body>

</html>