<?php
require_once '../../helper/connection.php';

// Get the id_pemesanan from URL
if (isset($_GET['id_pemesanan'])) {
    $id_pemesanan = $_GET['id_pemesanan'];

    // Fetch the order details and payment method from the database
    $query = "
        SELECT ps.*, p.metode_bayar
        FROM tb_pemesanan_show ps
        LEFT JOIN tb_pembayaran_show p ON ps.id_pemesanan = p.id_pemesanan
        WHERE ps.id_pemesanan = ?
    ";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $id_pemesanan);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
} else {
    echo "ID Pemesanan tidak ditemukan!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - GalaxyPass</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
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
                    <p><strong>Nama Pemesan:</strong> <span class="info-value"><?php echo htmlspecialchars($order['nama_user']); ?></span></p>
                    <p><strong>Tanggal:</strong> <span class="info-value"><?php echo htmlspecialchars($order['tgl']); ?></span></p>
                    <p><strong>Nama Instansi:</strong> <span class="info-value"><?php echo htmlspecialchars($order['nama_instansi']); ?></span></p>
                    <p><strong>Sesi:</strong> <span class="info-value"><?php echo htmlspecialchars($order['nama_show']); ?></span></p>
                    <p><strong>Jam:</strong> <span class="info-value"><?php echo htmlspecialchars($order['jam_mulai']) . ' - ' . htmlspecialchars($order['jam_selesai']); ?></span></p>
                    <p><strong>Jumlah Dewasa:</strong> <span class="info-value"><?php echo htmlspecialchars($order['jmlh_dewasa']); ?></span></p>
                    <p><strong>Jumlah Anak:</strong> <span class="info-value"><?php echo htmlspecialchars($order['jmlh_anak']); ?></span></p>
                    <p><strong>Total Bayar:</strong> <span class="info-value"><?php echo "Rp " . number_format($order['total_akhir'], 0, ',', '.'); ?></span></p>
                    <p><strong>Metode Pembayaran:</strong> <span class="info-value"><?php echo htmlspecialchars($order['metode_bayar']); ?></span></p>
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
        <a href="download_ticket.php?id=<?php echo $order['id_pemesanan']; ?>" class="btn btn-success">Download Tiket</a>
    </div>

<script>
// Generate Barcode from the order's kode_pemesanan (kode pemesanan)
JsBarcode("#barcode", "<?php echo $order['id_pemesanan']; ?>", {
    format: "CODE128", // You can change the format if needed
    width: 2,
    height: 80,  // Increased height for barcode
    displayValue: true, // Display the input value under the barcode
    fontSize: 18
});
</script>

</body>
</html>