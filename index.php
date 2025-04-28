<?php
include 'koneksi.php'; // Mengimpor file koneksi.php untuk menyambungkan ke database MySQL
session_start(); // Memulai sesi agar data pengguna (user) bisa diakses di seluruh halaman
session_regenerate_id(true); // Regenerasi session ID untuk keamanan, agar session tidak mudah dibajak

// Mengecek apakah pengguna sudah login atau belum
// Jika belum login (tidak ada data user di session), maka diarahkan ke halaman login
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit();
}

// Menyimpan ID pengguna dari session ke dalam variabel $id_user
// ID ini digunakan untuk menampilkan dan menyimpan data tugas berdasarkan pengguna
$id_user = $_SESSION['user']['id'];

// Mengecek apakah form tambah tugas telah dikirim
if (isset($_POST['add_task'])) {
    // Mengambil input dari form yang dikirim pengguna
    $task = $_POST['task']; // Nama tugas
    $priority = $_POST['priority']; // Prioritas tugas
    $date = $_POST['date']; // Tanggal tugas

    // Memastikan bahwa semua field (input) telah diisi
    if (!empty($task) && !empty($priority) && !empty($date)) {
        // Menyimpan waktu saat tugas dibuat (tanggal & jam lengkap)
        $created_at = date('Y-m-d H:i:s');
        // Menggunakan prepared statement untuk menghindari SQL Injection
        // Query INSERT data ke tabel 'tasks' dengan field task, priority, date, status (0 = belum selesai), id_user, dan created_at
        $stmt = mysqli_prepare($koneksi, "INSERT INTO tasks (task, priority, date, status, id_user, created_at) VALUES (?, ?, ?, 0, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssis", $task, $priority, $date, $id_user, $created_at);
        mysqli_stmt_execute($stmt);

          // Memberi alert jika berhasil dan mengarahkan ulang ke halaman index.php
        echo "<script>alert('data berhasil di simpan');</script>";
        header('Location: index.php');
        exit();
    } else {
        // Jika salah satu field kosong, tampilkan pesan kesalahan
        echo "<script>alert('semua kolom harus diisi.');</script>";
    }
}

// Jika tombol 'Selesai' diklik, ambil ID dari parameter URL (?completed=)
if (isset($_GET['completed'])) {
    $id = $_GET['completed'];
    // Lakukan update status di database menjadi 1 (selesai)
    mysqli_query($koneksi, "UPDATE tasks SET status = 1 WHERE id = $id");
    echo "<script>alert('data berhasil diperbarui');</script>";
    header('location:index.php');
}

// Jika tombol 'Hapus' diklik, ambil ID dari parameter URL (?delete=)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Hapus data dari tabel 'tasks' berdasarkan ID
    mysqli_query($koneksi, "DELETE FROM tasks WHERE id = $id");
    echo "<script>alert('data berhasil dihapus'); </script>";
    header('location:index.php');
}

// Program pemfilteran task atau tugas
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = $search ? " AND task LIKE '%$search%'" : '';

$status_query = "";
if ($filter === '0') {
    $status_query = " AND status = 0"; 
} elseif ($filter === '1') {
    $status_query = " AND status = 1"; 
}

// Ambil nilai keyword dari pencarian (jika ada)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = "";

// Jika ada kata kunci pencarian, tambahkan ke query SQL menggunakan LIKE
if ($search) {
    $search_query = " AND task LIKE '%$search%'";
}

// untuk menghapus semua tugas berdasarkan tiga level prioritas
if(isset($_GET['delete_by_priority']) && isset($_GET['delete_priority'])) {
    $delete_priority = $_GET['delete_priority'];

    if (in_array($delete_priority, [1, 2, 3])) {
        
        $delete_query = "DELETE FROM tasks WHERE id_user = '$id_user' AND priority = '$delete_priority'";
        $delete_result = mysqli_query($koneksi, $delete_query);

        if ($delete_result) {
            echo "<script>alert('All tasks with priority level " . ($delete_priority == 1 ? "Biasa" : ($delete_priority == 2 ? "Penting" : "Sangat Penting")) . " have been deleted successfully.');</script>";
        } else {
            echo "<script>alert('Error deleting tasks.');</script>";
        }
        
        header('Location: index.php');
        exit();
    } else {
        echo "<script>alert('Invalid priority selected.');</script>";
    }
}

// Query untuk menampilkan semua tugas milik user yang sedang login
// Tugas diurutkan berdasarkan status (belum selesai dulu), lalu prioritas, lalu tanggal
$result = mysqli_query($koneksi, "SELECT * FROM tasks WHERE id_user = '$id_user'$status_query$search_query ORDER BY status ASC, priority DESC, date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Mengatur agar halaman terlihat bagus di semua ukuran layar -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Document</title>

    <!-- Menghubungkan file CSS buatan sendiri untuk styling tambahan -->
    <link href="index.css" rel="stylesheet" type="text/css">

    <!-- Menggunakan Bootstrap CSS versi lokal (offline), untuk tampilan responsif -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body>
<!-- Container utama dari Bootstrap -->
<div class="container mt-2">

    <!-- Judul halaman -->
    <h2 class="text-center">To-Do List</h2>

    <!-- Area Logout + Tombol Collapse -->
    <div class="logout-container">
        <!-- Tombol untuk logout (menghapus session login) -->
        <a href="logout.php" class="btn btn-primary mt-3 mr-3">Logout</a>

        <!-- Tombol untuk menampilkan/sembunyikan form tambah task -->
        <button class="btn btn-primary mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#taskBox">
            Tambah Task (Collapse)
        </button>
    </div>

    <!-- Form untuk menambah task baru, tersembunyi secara default karena class "collapse" -->
    <div class="add-task-box collapse mt-3" id="taskBox">
        <form method="POST">
            <!-- Input nama tugas -->
            <label class="form-label">Nama Task</label>
            <input type="text" name="task" class="form-control" placeholder="Masukan Task Baru" autocomplete="off" autofocus required>

            <!-- Dropdown prioritas -->
            <label class="form-label">Prioritas</label>
            <select name="priority" class="form-control" required>
                <option value="">Pilih Prioritas</option>
                <option value="1">Biasa</option>
                <option value="2">Penting</option>
                <option value="3">Sangat Penting</option>
            </select>

            <!-- Input tanggal deadline -->
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>

            <!-- Tombol submit -->
            <button type="submit" class="btn btn-primary w-100 mt-2" name="add_task">Tambah</button>
        </form>
    </div>

    <!--Tombol task untuk menghapus semua task berdasarkan level-->
    <div class="delete-task-by-priority">
        <form method="GET" class="mb-3">
            <label for="delete_priority" class="form-label">Hapus Tugas dengan Prioritas</label>
            <select name="delete_priority" id="delete_priority" class="form-control" required>
                 <option value="">Pilih Prioritas</option>
                 <option value="1">Biasa</option>
                 <option value="2">Penting</option>
                 <option value="3">Sangat Penting</option>
            </select>
         <button type="submit" class="btn btn-danger mt-2 w-100" name="delete_by_priority">Delete Tasks</button>
        </form>
    </div>

    <hr>

    <!-- Kotak pencarian tugas -->
    <div class="search-box">
        <form method="GET" class="mb-3">
            <label class="form-search">Search</label>
            <!-- Input pencarian akan mengisi parameter ?search di URL -->
            <input type="text" name="search" class="form-control" placeholder="Search Task"
                value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" />
                <div class="btn-group mt-2 w-100">
                <button type="submit" class="btn btn-secondary">Search</button>
                 <a href="index.php?filter=all" class="btn btn-outline-primary <?= $filter === 'all' ? 'active' : '' ?>">Semua</a>
                 <a href="index.php?filter=0" class="btn btn-outline-primary <?= $filter === '0' ? 'active' : '' ?>">Tidak Selesai</a>
                 <a href="index.php?filter=1" class="btn btn-outline-primary <?= $filter === '1' ? 'active' : '' ?>">Sudah Selesai</a>
                </div>
        </form>
    </div>

    <hr>

    <!-- Tabel tugas akan ditampilkan dalam bentuk Bootstrap Card, bukan baris tabel biasa -->
    <table class="table table-striped">
        <tbody>
        <?php
        // Mengecek apakah hasil query ada isinya
        if (mysqli_num_rows($result) > 0) {
            $no = 1;
            // Loop untuk menampilkan setiap data tugas
            while ($rows = mysqli_fetch_assoc($result)) { ?>

                <!-- Kotak task (menggunakan Bootstrap Card) -->
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- Baris atas: Nama task + tombol collapse -->
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Judul tugas -->
                            <h5 class="card-title mb-0"><?= $no++ . ". " . $rows['task'] ?></h5>

                            <!-- Tombol untuk menampilkan detail tugas -->
                            <button class="btn btn-outline-primary btn-sm" type="button"
                                data-bs-toggle="collapse" data-bs-target="#detail<?= $rows['id'] ?>">
                                Details
                            </button>
                        </div>

                        <!-- Konten detail task (hidden secara default) -->
                        <div class="collapse mt-3" id="detail<?= $rows['id'] ?>">
                            <ul class="list-group list-group-flush">

                                <!-- Menampilkan prioritas -->
                                <li class="list-group-item"><strong>Prioritas:</strong>
                                    <?= $rows['priority'] == 1 ? "Biasa" : ($rows['priority'] == 2 ? "Penting" : "Sangat Penting") ?>
                                </li>

                                <!-- Menampilkan tanggal tugas -->
                                <li class="list-group-item"><strong>Tanggal:</strong> <?= $rows['date'] ?></li>

                                <!-- Menampilkan status -->
                                <li class="list-group-item"><strong>Status:</strong>
                                    <?= $rows['status'] == 0 ? "Belum Selesai" : "Sudah Selesai" ?>
                                </li>

                                <!-- Menampilkan waktu dibuat -->
                                <li class="list-group-item"><strong>Jam dibuat: </strong>
                                    <?= date('H:i:s', strtotime($rows['created_at'])) ?>
                                </li>

                                <!-- Tombol aksi -->
                                <li class="list-group-item">
                                    <?php if ($rows['status'] == 0) { ?>
                                        <!-- Tombol untuk menandai tugas selesai -->
                                        <a href="?completed=<?= $rows['id'] ?>" class="btn btn-success btn-sm">Selesai</a>
                                    <?php } ?>
                                    <!-- Tombol hapus dan edit -->
                                    <a href="?delete=<?= $rows['id'] ?>" class="btn btn-danger btn-sm">Hapus</a>
                                    <a href="edit_task.php?id=<?= $rows['id'] ?>" class="btn btn-warning btn-sm">Modify</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php }
        } ?>
        </tbody>
    </table>
</div>

<!-- Menjalankan fitur Bootstrap Collapse dan komponen lainnya -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
</body>
</html>