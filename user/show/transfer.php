<?php
require_once '../../helper/connection.php';

// Ambil id_pemesanan dari parameter GET
$id_pemesanan = isset($_GET['id_pemesanan']) ? $_GET['id_pemesanan'] : '';

// Query untuk mengambil data pembayaran berdasarkan id_pemesanan
$query_pembayaran = "
    SELECT p.id_pembayaran, p.id_pemesanan, p.metode_bayar, p.bank, p.jumlah_bayar, p.status_bayar
    FROM tb_pembayaran_show p
    JOIN tb_pemesanan_show pem ON pem.id_pemesanan = p.id_pemesanan
    WHERE pem.id_pemesanan = ?";
$stmt_pembayaran = $connection->prepare($query_pembayaran);
$stmt_pembayaran->bind_param('i', $id_pemesanan);
$stmt_pembayaran->execute();
$result_pembayaran = $stmt_pembayaran->get_result();

if ($result_pembayaran->num_rows === 0) {
    echo "<script>alert('Pembayaran tidak ditemukan untuk pemesanan ini.'); window.history.back();</script>";
    exit;
}

$pembayaran = $result_pembayaran->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Transfer Pembayaran &mdash; Galaxy Pass</title>
  <!-- CSS Khusus Halaman Transfer -->
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="transfer-page">
  <div class="container galaxy-transfer-page">
      <h2 class="galaxy-title">Transfer Pembayaran</h2>

      <!-- Informasi Pembayaran -->
      <div class="galaxy-info-card">
          <p><strong>Kode Pemesanan:</strong> <?php echo $pembayaran['id_pemesanan']; ?></p>
          <p><strong>Jumlah Pembayaran:</strong> Rp <?php echo number_format($pembayaran['jumlah_bayar'], 0, ',', '.'); ?></p>
          <p><strong>Bank yang Dipilih:</strong> <?php echo $pembayaran['bank']; ?></p>
      </div>

      <!-- Timer -->
      <div id="timer" class="galaxy-timer">
          Waktu Tersisa: <span id="countdown">05:00</span>
      </div>

      <!-- QR Code -->
      <div class="galaxy-qr-card">
          <h3>Scan QR Code Pembayaran</h3>
          <img src="../../assets/img/QR.jpeg" alt="QR Code" class="galaxy-qr-code">
      </div>

      <!-- Form Upload Bukti Pembayaran -->
      <form action="proses_upload.php" method="POST" enctype="multipart/form-data" class="galaxy-upload-form">
          <input type="hidden" name="id_pemesanan" value="<?php echo $pembayaran['id_pemesanan']; ?>"> 
          
          <div class="galaxy-file-upload">
                <label for="bukti_bayar" class="galaxy-file-label">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Bukti Pembayaran
                </label>
                <input type="file" name="bukti_bayar" id="bukti_bayar" class="galaxy-file-input" required>
            </div>

          
          <button type="submit" class="galaxy-submit-btn">Kirim Bukti Pembayaran</button>
      </form>
  </div>

  <!-- Timer Script -->
  <script>
    var timeLimit = 5 * 60;

    var countdownInterval = setInterval(function() {
        var minutes = Math.floor(timeLimit / 60);
        var seconds = timeLimit % 60;

        document.getElementById("countdown").textContent = 
            (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

        if (timeLimit <= 0) {
            clearInterval(countdownInterval);
            document.getElementById("timer").textContent = "Waktu Habis";
            document.querySelector(".galaxy-upload-form").style.display = "none";
            alert("Waktu telah habis! Anda tidak dapat mengirim bukti pembayaran.");
        }

        timeLimit--;
    }, 1000);
  </script>
</body>
</html>
