<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$alertMessage = '';
if (!empty($_GET['pesan'])) {
    switch ($_GET['pesan']) {
        case 'login_kosong':
            $alertMessage = 'Username atau Password Kosong';
            $alertType = 'danger';
            break;
        case 'login_salah':
            $alertMessage = 'Username atau Password Tidak Ada';
            $alertType = 'danger';
            break;
        case 'logout_berhasil':
            $alertMessage = 'Logout Berhasil';
            $alertType = 'success';
            break;
    }
}

$accounts = [];
$query = mysqli_query($koneksi, "SELECT username FROM user");
while ($row = mysqli_fetch_assoc($query)) {
    $accounts[] = $row['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    
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

            <?php if ($alertMessage): ?>
                <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
                    <strong>Info:</strong> <?= $alertMessage ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="login_proses.php" method="post">
                <h2 class="text-center">Login</h2>

                <div class="mb-3">
                    <button class="btn btn-secondary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#accountList">Lihat Semua Akun</button>
                    <div class="collapse mt-2" id="accountList">
                        <ul class="list-group">
                        <?php foreach ($accounts as $acc): ?>
                         <li class="list-group-item"><?= htmlspecialchars($acc) ?></li>
                        <?php endforeach; ?>
                    </ul>
                 </div>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" id="username" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                     <input type="password" class="form-control" name="password" id="password" required>
                     <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                        <i class="bi-bi-eye-slash"></i>
                    </span>
                 </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <a href="signin.php" class="btn btn-link w-100 text-center d-block mt-3">Daftar Akun</a>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script>
    const toggle = document.querySelector('#togglePassword');
    const icon = toggle.querySelector('i');
    const password = document.querySelector('#password');

    toggle.addEventListener('click', () => {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
</script>

</body>
</html>
