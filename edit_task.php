<?php
// Menghubungkan ke database
include 'koneksi.php';
session_start();

// Mengecek apakah user sudah login, jika tidak maka diarahkan ke login
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

// Mengecek apakah ada parameter 'id' yang dikirim melalui URL (GET)
if (isset($_GET['id'])) {
    $id_task = $_GET['id'];

    // Mengambil data task berdasarkan ID dari database
    $result = mysqli_query($koneksi, "SELECT * FROM tasks WHERE id = $id_task");
    $task_data = mysqli_fetch_assoc($result);

    // Jika data tidak ditemukan, kembali ke halaman utama
    if (!$task_data) {
        echo "<script>alert('Task tidak ditemukan!');</script>";
        header('Location: index.php');
        exit();
    }
}

// Jika tombol 'update_task' ditekan
if (isset($_POST['update_task'])) {
    $new_task_name = $_POST['task_name']; // Nama tugas yang baru
    $new_priority = $_POST['priority'];   // Prioritas baru dari select input

    // Validasi: tidak boleh kosong
    if (!empty($new_task_name) && !empty($new_priority)) {
        // Proses update data di database
        mysqli_query($koneksi, "UPDATE tasks SET task = '$new_task_name', priority = '$new_priority' WHERE id = $id_task");

        echo "<script>alert('Task berhasil diperbarui!');</script>";
        header('Location: index.php'); // Kembali ke halaman utama
    } else {
        echo "<script>alert('Task dan prioritas tidak boleh kosong!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Task</h2>
    <!-- Form edit tugas -->
    <form method="POST">
        <!-- Input untuk mengubah nama task -->
        <div class="mb-3">
            <label class="form-label">Nama Task</label>
            <input type="text" name="task_name" class="form-control" value="<?php echo $task_data['task']; ?>" required>
        </div>

        <!-- Dropdown untuk mengubah prioritas -->
        <div class="mb-3">
            <label class="form-label">Prioritas</label>
            <select name="priority" class="form-control" required>
                <option value="1" <?= $task_data['priority'] == 1 ? 'selected' : '' ?>>Biasa</option>
                <option value="2" <?= $task_data['priority'] == 2 ? 'selected' : '' ?>>Penting</option>
                <option value="3" <?= $task_data['priority'] == 3 ? 'selected' : '' ?>>Sangat Penting</option>
            </select>
        </div>

        <!-- Tombol aksi -->
        <button type="submit" name="update_task" class="btn btn-primary">Update Task</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>