<?php
require_once '../../helper/connection.php';

// Ambil ID Pemesanan dan Pilihan Bank dari URL
$id_pemesanan = $_GET['id_pemesanan'];
$pilih_bank = $_GET['pilih_bank']; // Bank yang dipilih oleh pengguna

// Ambil data pemesanan berdasarkan ID Pemesanan untuk mendapatkan total bayar
$sql = "SELECT p.*, e.harga 
        FROM tb_pemesanan_event p
        JOIN tb_jadwal_event e ON p.id_event = e.id_event
        WHERE p.id_pemesanan = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $id_pemesanan);
$stmt->execute();
$result = $stmt->get_result();
$pemesanan = $result->fetch_assoc();

// Total bayar
$total_bayar = $pemesanan['jmlh_orang'] * $pemesanan['harga'];

// Data rekening bank
$rekening_sql = "SELECT * FROM tb_rekening WHERE id_bank = ?";
$stmt_rekening = $connection->prepare($rekening_sql);
$stmt_rekening->bind_param("s", $pilih_bank);
$stmt_rekening->execute();
$rekening_result = $stmt_rekening->get_result();
$rekening = $rekening_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran - Event</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="transfer-page">
  <div class="container galaxy-transfer-page">
      <h2 class="galaxy-title">Upload Bukti Pembayaran - Event</h2>

      <!-- Informasi Pemesanan -->
      <div class="galaxy-info-card">
          <p><strong>No. Pemesanan:</strong> <?php echo $id_pemesanan; ?></p>
          <p><strong>Total Bayar:</strong> Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></p>
      </div>

      <!-- Timer -->
      <div id="timer" class="galaxy-timer">
          Waktu Tersisa: <span id="countdown">10:00</span>
      </div>

      <!-- QR Code -->
      <div class="galaxy-qr-card">
          <h3>Scan QR Code Pembayaran</h3>
          <img src="../../assets/img/QR.jpeg" alt="QR Code" class="galaxy-qr-code">
      </div>

      <!-- Informasi Rekening -->
      <div class="galaxy-bank-info">
          <h3>Informasi Rekening Bank</h3>
          <p><strong>Bank:</strong> <?php echo $rekening['nama_bank']; ?></p>
          <p><strong>Nomor Rekening:</strong> <?php echo $rekening['norek']; ?></p>
          <p><strong>Atas Nama:</strong> <?php echo $rekening['atas_nama']; ?></p>
      </div>

      <!-- Form Upload Bukti Pembayaran -->
      <form action="proses_upload.php" method="POST" enctype="multipart/form-data" class="galaxy-upload-form">
          <input type="hidden" name="id_pemesanan" value="<?php echo $id_pemesanan; ?>">
          <input type="hidden" name="pilih_bank" value="<?php echo $rekening['nama_bank']; ?>">

          <div class="galaxy-file-upload">
              <label for="bukti_bayar" class="galaxy-file-label">
                  <i class="fas fa-cloud-upload-alt"></i> Upload Bukti Pembayaran
              </label>
              <input type="file" name="bukti_bayar" id="bukti_bayar" class="galaxy-file-input" required>
          </div>

          <button type="submit" class="galaxy-submit-btn">Kirim Bukti Pembayaran</button>
      </form>
  </div>

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
      const timerInterval = setInterval(updateTimer, 1000);
      updateTimer();
    // Menangani pemilihan metode pembayaran
    document.querySelectorAll('input[name="metode_bayar"]').forEach(function(input) {
        input.addEventListener('change', function() {
            if (this.value === 'cash') {
                document.getElementById('cash-actions').style.display = 'block';
                document.getElementById('rekening-list').style.display = 'none';
                document.getElementById('upload-form').style.display = 'none';
            } else if (this.value === 'transfer') {
                document.getElementById('rekening-list').style.display = 'block';
                document.getElementById('cash-actions').style.display = 'none';
                document.getElementById('upload-form').style.display = 'none';
            }
        });
    });

    // Menangani tombol batal dan ganti untuk cash
    document.getElementById('btn-batal').addEventListener('click', function() {
        document.getElementById('payment-method').style.display = 'none';
        document.getElementById('cash-actions').style.display = 'none';
    });

    document.getElementById('btn-ganti').addEventListener('click', function() {
        document.getElementById('payment-method').style.display = 'block';
        document.getElementById('cash-actions').style.display = 'none';
    });
</script>

</body>
</html>
