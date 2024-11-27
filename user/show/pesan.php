<?php
require_once '../../helper/connection.php';

// Ambil informasi jadwal dari URL setelah tombol pilih ditekan
$id_show = isset($_GET['id_show']) ? $_GET['id_show'] : '';
$selected_date = isset($_GET['date']) ? $_GET['date'] : '';

// Query untuk mendapatkan informasi show dan harga berdasarkan id_jadwal
$query_show = "
    SELECT tb_jadwal_show.nama_show, 
           tb_jadwal_show.jam_mulai, 
           tb_jadwal_show.jam_selesai, 
           tb_harga.harga_dewasa, 
           tb_harga.harga_anak
    FROM tb_jadwal_show
    JOIN tb_harga ON tb_jadwal_show.id_harga = tb_harga.id_harga
    WHERE tb_jadwal_show.id_jadwal = ?
";
$stmt_show = $connection->prepare($query_show);
$stmt_show->bind_param('i', $id_show);
$stmt_show->execute();
$result_show = $stmt_show->get_result();
$data_show = $result_show->fetch_assoc();

function getBookingsCount($id_show, $tanggal) {
    global $connection;

    // SQL untuk menghitung total pemesanan untuk id_show dan tanggal tertentu
    $sql = "
        SELECT SUM(jmlh_anak + jmlh_dewasa) AS total_orang 
        FROM tb_pemesanan_show 
        WHERE id_show = ? AND tgl = ?
    ";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("is", $id_show, $tanggal); // Parameter "is" berarti integer dan string
    $stmt->execute();
    $stmt->bind_result($total_orang);
    $stmt->fetch();

    // Pastikan mengembalikan 0 jika tidak ada data
    return $total_orang ? $total_orang : 0;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Show</title>
    <!-- Link ke CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="galaxy-form"></body>
<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Pesan Tiket Show</h1>
        <div class="d-flex align-items-center">
            <a href="./jadwal.php" class="btn btn-light mr-2">Batal</a>
        </div>
    </div>
    <form action="store.php" method="POST" class="galaxy-form">
        <!-- Nama Lengkap -->
        <div class="mb-3">
            <label for="nama_user" class="form-label">Nama Lengkap</label>
            <input type="text" id="nama_user" name="nama_user" class="form-control" required>
        </div>
        
        <!-- Nomor HP -->
        <div class="mb-3">
            <label for="no_hp" class="form-label">Nomor HP</label>
            <input type="tel" id="no_hp" name="no_hp" class="form-control" required>
        </div>
        
        <!-- Instansi -->
        <div class="mb-3">
            <label for="nama_instansi" class="form-label">Instansi</label>
            <input type="text" id="nama_instansi" name="nama_instansi" class="form-control" required>
        </div>
        
        <!-- Nama Show -->
        <div class="mb-3">
            <label for="nama_show" class="form-label">Nama Show</label>
            <input type="text" id="nama_show" name="nama_show" value="<?= htmlspecialchars($data_show['nama_show']) ?>" class="form-control" readonly required>
        </div>
        
        <!-- Tanggal -->
        <div class="mb-3">
            <label for="tgl" class="form-label">Tanggal</label>
            <input type="date" id="tgl" name="tgl" value="<?= htmlspecialchars($selected_date) ?>" class="form-control" readonly required>
        </div>
        
        <!-- Jam Mulai -->
        <div class="mb-3">
            <label for="jam_mulai" class="form-label">Jam Mulai</label>
            <input type="time" id="jam_mulai" name="jam_mulai" value="<?= htmlspecialchars(substr($data_show['jam_mulai'], 0, 5)) ?>" class="form-control" readonly required>
        </div>
        
        <!-- Jam Selesai -->
        <div class="mb-3">
            <label for="jam_selesai" class="form-label">Jam Selesai</label>
            <input type="time" id="jam_selesai" name="jam_selesai" value="<?= htmlspecialchars(substr($data_show['jam_selesai'], 0, 5)) ?>" class="form-control" readonly required>
        </div>
        
        <!-- Jumlah Dewasa -->
        <div class="mb-3">
            <label for="jmlh_dewasa" class="form-label">Jumlah Dewasa</label>
            <input type="number" id="jmlh_dewasa" name="jmlh_dewasa" class="form-control" required>
        </div>
        
        <!-- Jumlah Anak -->
        <div class="mb-3">
            <label for="jmlh_anak" class="form-label">Jumlah Anak</label>
            <input type="number" id="jmlh_anak" name="jmlh_anak" class="form-control" required>
        </div>
        
        <!-- Hidden Inputs -->
        <input type="hidden" name="id_jadwal" value="<?= htmlspecialchars($id_show) ?>">
        <input type="hidden" name="username" value="<?= htmlspecialchars($_SESSION['login']['username']) ?>">
        <input type="hidden" name="harga_dewasa" value="<?= htmlspecialchars($data_show['harga_dewasa']) ?>">
        <input type="hidden" name="harga_anak" value="<?= htmlspecialchars($data_show['harga_anak']) ?>">

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="btn btn-success w-100">Pesan Tiket</button>
        </div>
    </form>
    </div>
</section>
</body>
</html>