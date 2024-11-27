<?php
require_once '../../helper/connection.php';

// Fungsi konversi hari ke bahasa Indonesia
function convertDayToIndonesian($dayInEnglish) {
    $days = [
        "Monday" => "Senin",
        "Tuesday" => "Selasa",
        "Wednesday" => "Rabu",
        "Thursday" => "Kamis",
        "Friday" => "Jumat",
        "Saturday" => "Sabtu",
        "Sunday" => "Minggu"
    ];

    return $days[$dayInEnglish] ?? $dayInEnglish;
}

// Ambil tanggal dari parameter atau gunakan hari ini
$selected_date = $_GET['date'] ?? date('Y-m-d');
$selected_day = date('l', strtotime($selected_date));
$selected_day_indonesian = convertDayToIndonesian($selected_day);

try {
    // Query jadwal dengan kapasitas tersisa
    $query = "
        SELECT 
            tb_jadwal_show.id_jadwal, 
            tb_jadwal_show.nama_show, 
            tb_jadwal_show.jam_mulai, 
            tb_jadwal_show.jam_selesai, 
            tb_jadwal_show.kapasitas - IFNULL(SUM(tb_pemesanan_show.jmlh_dewasa + tb_pemesanan_show.jmlh_anak), 0) AS kapasitas_tersisa,
            tb_harga.harga_dewasa, 
            tb_harga.harga_anak
        FROM tb_jadwal_show
        LEFT JOIN tb_pemesanan_show 
            ON tb_jadwal_show.id_jadwal = tb_pemesanan_show.id_pemesanan 
            AND tb_pemesanan_show.tgl = ?
        JOIN tb_harga 
            ON tb_jadwal_show.id_harga = tb_harga.id_harga
        WHERE FIND_IN_SET(?, tb_jadwal_show.hari_khusus) > 0
        GROUP BY tb_jadwal_show.id_jadwal
        ORDER BY tb_jadwal_show.jam_mulai
    ";

    $stmt = $connection->prepare($query);
    $stmt->bind_param('ss', $selected_date, $selected_day_indonesian);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    die("Terjadi kesalahan saat mengambil data jadwal: " . $e->getMessage());
}

// Fungsi untuk menghitung jumlah pemesanan
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
    <link rel="stylesheet" href="../../assets/css/jadwal.css">
</head>
<body class="galaxy-theme">
<section class="section-show">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Jadwal Show</h1>
        <form action="" method="GET" class="d-flex align-items-center">
            <input type="date" name="date" value="<?= htmlspecialchars($selected_date) ?>" class="form-control me-2" required>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>
    </div>

    <div class="row">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($data = $result->fetch_assoc()): ?>
            <div class="col-md-12 mb-4">
                <div class="card event-card shadow-lg d-flex flex-row align-items-center show-card">
                    <div class="card-body d-flex justify-content-between align-items-center w-100">
                        <div class="show-info d-flex flex-row w-100 justify-content-between">
                            <div class="show-section me-4 border-right">
                                <h5 class="card-title"><?= htmlspecialchars($data['nama_show']) ?></h5>
                            </div>
                            <div class="show-section me-4 border-right">
                                <p><strong>Jam:</strong> <?= htmlspecialchars(substr($data['jam_mulai'], 0, 5)) ?> - <?= htmlspecialchars(substr($data['jam_selesai'], 0, 5)) ?></p>
                            </div>
                            <div class="show-section me-4 border-right">
                                <p><strong>Sisa Kursi:</strong> <?= htmlspecialchars($data['kapasitas_tersisa']) ?> orang</p>
                            </div>
                            <div class="show-section price">
                                <p><strong>Harga:</strong></p>
                                <p>Rp<?= number_format($data['harga_dewasa'], 0, ',', '.') ?>/dewasa</p>
                                <p>Rp<?= number_format($data['harga_anak'], 0, ',', '.') ?>/anak (&lt;12 thn)</p>
                            </div>
                            <div class="text-button-wrapper">
                            <form action="pesan.php" method="GET">
                                <input type="hidden" name="id_show" value="<?= htmlspecialchars($data['id_jadwal']) ?>">
                                <input type="hidden" name="date" value="<?= htmlspecialchars($selected_date) ?>">
                                <button class="btn btn-primary select-button" type="submit">Pilih</button>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <p class="text-center">Maaf, tidak ada jadwal show pada hari <?= htmlspecialchars($selected_day_indonesian) ?>, <?= htmlspecialchars($selected_date) ?>.</p>
        </div>
    <?php endif; ?>
</div>
</section>
</body>
</html>
