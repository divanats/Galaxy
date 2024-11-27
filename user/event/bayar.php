<?php
require_once '../../helper/connection.php';

// Ambil ID Pemesanan dari URL
$id_pemesanan = isset($_GET['id_pemesanan']) ? $_GET['id_pemesanan'] : null;

if ($id_pemesanan) {
    // Query untuk mengambil data pemesanan berdasarkan id_pemesanan
    $sql = "SELECT * FROM tb_pemesanan_event WHERE id_pemesanan = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id_pemesanan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pemesanan = $result->fetch_assoc();
        $id_event = $pemesanan['id_event'];

        // Ambil data event untuk menghitung total bayar
        $sql_event = "SELECT * FROM tb_jadwal_event WHERE id_event = ?";
        $stmt_event = $connection->prepare($sql_event);
        $stmt_event->bind_param("i", $id_event);
        $stmt_event->execute();
        $event_result = $stmt_event->get_result();
        $event = $event_result->fetch_assoc();
        $total_bayar = $pemesanan['jmlh_orang'] * $event['harga'];

        // Ambil data rekening bank
        $sql_rekening = "SELECT * FROM tb_rekening";
        $stmt_rekening = $connection->prepare($sql_rekening);
        $stmt_rekening->execute();
        $rekening_result = $stmt_rekening->get_result();
    } else {
        echo '<p>No pemesanan found with that ID.</p>';
        exit;
    }
} else {
    echo '<p>Invalid pemesanan ID.</p>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/bayar.css">
</head>
<body class="galaxy-theme">
    <div class="payment-card">
        <h1>Pembayaran Pesanan</h1>

        <!-- Informasi Pemesanan -->
        <div class="data-row">
            <span class="data-label"><strong>No. Booking:</strong></span>
            <span class="data-value"><?php echo $id_pemesanan; ?></span>
        </div>
        <div class="data-row">
            <span class="data-label"><strong>Nama Pemesan:</strong></span>
            <span class="data-value"><?php echo $pemesanan['nama_user']; ?></span>
        </div>
        <div class="data-row">
            <span class="data-label"><strong>Jumlah Orang:</strong></span>
            <span class="data-value"><?php echo $pemesanan['jmlh_orang']; ?></span>
        </div>
        <div class="data-row">
            <span class="data-label"><strong>Harga per Orang:</strong></span>
            <span class="data-value">Rp<?php echo number_format($event['harga'], 0, ',', '.'); ?></span>
        </div>
        <div class="data-row">
            <span class="data-label"><strong>Total Bayar:</strong></span>
            <span class="data-value">Rp<?php echo number_format($total_bayar, 0, ',', '.'); ?></span>
        </div>

        <!-- Formulir Pembayaran -->
        <form method="POST" action="proses_bayar.php" enctype="multipart/form-data">
            <input type="hidden" name="id_pemesanan" value="<?php echo $id_pemesanan; ?>">
            <input type="hidden" name="jumlah_bayar" value="<?php echo $total_bayar; ?>">

            <!-- Pilihan Metode Pembayaran -->
            <div class="select-container">
                <label for="metode_bayar" class="data-label">Metode Pembayaran</label>
                <select id="metode_bayar" name="metode_bayar" required onchange="toggleBankDetails()">
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>

            <!-- Pilihan Rekening Bank, disembunyikan awalnya -->
            <div id="bank-details" class="select-container" style="display: none;">
                <label for="pilih_bank" class="data-label">Pilih Bank</label>
                <select id="pilih_bank" name="pilih_bank">
                    <?php while ($rekening = $rekening_result->fetch_assoc()): ?>
                        <option value="<?php echo $rekening['id_bank']; ?>">
                            <?php echo $rekening['nama_bank'] . ' - ' . $rekening['norek'] . ' a.n. ' . $rekening['atas_nama']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Tombol Pesan -->
            <button type="submit" class="payment-btn">Pesan Sekarang</button>
        </form>
    </div>

    <!-- Skrip Interaktif -->
    <script>
        // Fungsi untuk menampilkan pilihan rekening bank jika metode pembayaran adalah transfer
        function toggleBankDetails() {
            const metodeBayar = document.getElementById("metode_bayar").value;
            const bankDetails = document.getElementById("bank-details");

            if (metodeBayar === "transfer") {
                bankDetails.style.display = "block"; // Tampilkan div rekening bank
            } else {
                bankDetails.style.display = "none";  // Sembunyikan div rekening bank
            }
        }

        // Menampilkan pesan pop-up jika memilih metode pembayaran cash
        document.querySelector('form').addEventListener('submit', function(event) {
            const metodeBayar = document.getElementById('metode_bayar').value;

            if (metodeBayar === 'cash') {
                alert('Anda memilih metode pembayaran cash. Silakan bayar langsung di tempat.');
            }
        });
    </script>
</body>

