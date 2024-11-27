<?php
require_once '../../helper/connection.php';

// Ambil ID Pemesanan dari URL
$id_pemesanan = $_GET['id_pemesanan'];

// Ambil data pemesanan berdasarkan ID Pemesanan
$sql = "SELECT p.*, e.nama_event, e.tgl, e.jam_mulai, e.jam_selesai, e.harga, b.status_bayar 
        FROM tb_pemesanan_event p
        JOIN tb_jadwal_event e ON p.id_event = e.id_event
        LEFT JOIN tb_pembayaran_event b ON p.id_pemesanan = b.id_pemesanan
        WHERE p.id_pemesanan = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $id_pemesanan);
$stmt->execute();
$result = $stmt->get_result();
$pemesanan = $result->fetch_assoc();

// Hitung total harga
$total_harga = $pemesanan['jmlh_orang'] * $pemesanan['harga'];

// Status pembayaran
$status_bayar = $pemesanan['status_bayar'] == 'sudah bayar' ? 'Lunas' : ($pemesanan['status_bayar'] == 'menunggu konfirmasi' ? 'Menunggu Konfirmasi' : 'Belum Bayar');

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - GalaxyPass</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="galaxy-theme">
<section class="section">
    <div class="section-header">
        <h1>Galaxy Pass</h1> <!-- Tulisan Galaxy Pass dipindahkan ke sini -->
    </div>
</section>
<section class="tiket-container">
    <div class="ticket-card">
        <!-- Watermark -->
        <div class="watermark">GalaxyPass</div>
        
        <!-- Header Card hanya berisi logo -->
        <div class="ticket-header">
            <div class="header-left">
                <img src="../../assets/img/logoku.png" alt="Logo" class="ticket-logo">
            </div>
            <div class="header-right">
                <h1>Galaxy Pass</h1>
            </div>
        </div>

        <!-- Ticket Content -->
        <div class="ticket-content">
            <div class="ticket-row">
                <!-- Informasi Pemesan -->
                <div class="ticket-info">
                    <p><strong>No. Booking:</strong> <span class="info-value"><?php echo $pemesanan['id_pemesanan']; ?></span></p>
                    <p><strong>Nama Event:</strong> <span class="info-value"><?php echo $pemesanan['nama_event']; ?></span></p>
                    <p><strong>Tanggal:</strong> <span class="info-value"><?php echo date('d m Y', strtotime($pemesanan['tgl'])); ?></span></p>
                    <p><strong>Jam:</strong> <span class="info-value">
                        <?php echo date('H:i', strtotime($pemesanan["jam_mulai"])) . ' - ' . (!empty($pemesanan["jam_selesai"]) ? date("H:i", strtotime($pemesanan["jam_selesai"])) : "Selesai") ?> WIB
                    </span></p>
                    <p><strong>Jumlah Orang:</strong> <span class="info-value"><?php echo $pemesanan['jmlh_orang']; ?> orang</span></p>
                    <p><strong>Total Harga:</strong> <span class="info-value">Rp<?php echo number_format($total_harga, 0, ',', '.'); ?></span></p>
                    <p><strong>Status Pembayaran:</strong> 
                        <span class="status <?php echo ($status_bayar == 'Lunas') ? 'lunas' : (($status_bayar == 'Menunggu Konfirmasi') ? 'menunggu-konfirmasi' : 'belum-bayar'); ?>">
                            <?php echo $status_bayar; ?>
                        </span>
                    </p>
                </div>

                <!-- Barcode Section -->
                <div class="barcode-section-wrapper">
                    <!-- Garis Vertikal -->
                    <div class="vertical-line"></div>
                    
                    <!-- Barcode Section -->
                    <div class="barcode-section">
                        <h4 class="highlight">Kode Pemesanan</h4>
                        <svg id="barcode"></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
 <!-- Buttons -->
 <div class="button-container">
        <a href="../dashboard/index.php" class="btn btn-primary">Kembali ke Dashboard</a>
        <a href="download_tiket.php?id=<?php echo $pemesanan['id_pemesanan']; ?>" class="btn btn-success">Download Tiket</a>
    </div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const idPemesanan = "<?php echo $pemesanan['id_pemesanan']; ?>";
    if (idPemesanan) {
        JsBarcode("#barcode", idPemesanan, {
            format: "CODE128",
            width: 2,
            height: 80,
            displayValue: true,
            fontSize: 18
        });
    } else {
        console.error("ID Pemesanan tidak tersedia");
    }
});

</script>

</body>
</html>