<?php
require_once '../../helper/connection.php';

$id_pemesanan = isset($_GET['id_pemesanan']) ? $_GET['id_pemesanan'] : '';

// Query untuk mendapatkan harga berdasarkan id_jadwal
$query_harga = "
    SELECT h.harga_anak, h.harga_dewasa 
    FROM tb_harga h
    JOIN tb_jadwal_show j ON h.id_harga = j.id_harga
    WHERE j.id_jadwal = ?
";
$stmt_harga = $connection->prepare($query_harga);
$stmt_harga->bind_param('i', $id_pemesanan);  // Mengasumsikan id_pemesanan sesuai dengan id_jadwal
$stmt_harga->execute();
$result_harga = $stmt_harga->get_result();

if ($result_harga->num_rows > 0) {
    $data_harga = $result_harga->fetch_assoc();
    $harga_anak = $data_harga['harga_anak'];
    $harga_dewasa = $data_harga['harga_dewasa'];
} else {
    $harga_anak = 0;
    $harga_dewasa = 0;
    echo " ";
}

$query_pemesanan = "
    SELECT jmlh_anak, jmlh_dewasa, total_awal 
    FROM tb_pemesanan_show 
    WHERE id_pemesanan = ?
";
$stmt_pemesanan = $connection->prepare($query_pemesanan);
$stmt_pemesanan->bind_param('i', $id_pemesanan);
$stmt_pemesanan->execute();
$result_pemesanan = $stmt_pemesanan->get_result();

$jmlh_anak = 0;
$jmlh_dewasa = 0;
$total_awal = 0;

if ($result_pemesanan->num_rows > 0) {
    $data_pemesanan = $result_pemesanan->fetch_assoc();
    $jmlh_anak = $data_pemesanan['jmlh_anak'];
    $jmlh_dewasa = $data_pemesanan['jmlh_dewasa'];
    $total_awal = $data_pemesanan['total_awal'];
}
$nilai_potongan = 0;

$query_vouchers = "SELECT kode_voucher, nilai_potongan FROM tb_voucher";
$result_vouchers = $connection->query($query_vouchers);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <!-- Link ke CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/bayar.css">
    </head>
    <body class="galaxy-theme">
    <div class="payment-card">
        <h1>Pembayaran Pesanan</h1>

        <!-- Formulir Pembayaran -->
        <form action="proses_bayar.php" method="POST">
            <!-- Informasi Data -->
            <div class="data-row">
                <span class="data-label">No Booking:</span>
                <span class="data-value"><?= htmlspecialchars($id_pemesanan) ?></span>
                <input type="hidden" name="id_pemesanan" value="<?= htmlspecialchars($id_pemesanan) ?>">
            </div>
            <div class="data-row">
                <span class="data-label">Jumlah Anak:</span>
                <span class="data-value"><?= $jmlh_anak ?></span>
                <input type="hidden" name="jmlh_anak" value="<?= $jmlh_anak ?>">
            </div>
            <div class="data-row">
                <span class="data-label">Jumlah Dewasa:</span>
                <span class="data-value"><?= $jmlh_dewasa ?></span>
                <input type="hidden" name="jmlh_dewasa" value="<?= $jmlh_dewasa ?>">
            </div>
            <div class="data-row">
                <span class="data-label">Total Awal:</span>
                <span class="data-value">Rp. <?= number_format($total_awal, 0, ',', '.') ?></span>
                <input type="hidden" name="total_awal" value="<?= $total_awal ?>">
            </div>

            <!-- Pilihan Voucher -->
            <div class="select-container">
                <label for="voucher" class="data-label">Pilih Voucher:</label>
                <select id="voucher" name="voucher">
                    <option value="">-- Pilih Voucher --</option>
                    <?php while ($voucher = $result_vouchers->fetch_assoc()): ?>
                        <option value="<?= $voucher['kode_voucher'] ?>" data-nilai_potongan="<?= $voucher['nilai_potongan'] ?>">
                            <?= $voucher['kode_voucher'] ?> - Potongan Rp. <?= number_format($voucher['nilai_potongan'], 0, ',', '.') ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Potongan dan Total Akhir -->
            <div class="data-row">
                <span class="data-label">Potongan Voucher:</span>
                <span class="data-value" id="potongan-value">Rp. 0</span>
            </div>
            <div class="data-row">
                <span class="data-label">Total Akhir:</span>
                <span class="data-value" id="total-akhir">Rp. <?= number_format($total_awal, 0, ',', '.') ?></span>
            </div>

            <!-- Pilihan Metode Pembayaran -->
            <div class="select-container">
                <label for="metode_bayar" class="data-label">Metode Pembayaran:</label>
                <select id="metode_bayar" name="metode_bayar" required>
                    <option value="">-- Pilih Metode Pembayaran --</option>
                    <option value="cash">Bayar Cash</option>
                    <option value="transfer">Bayar Transfer</option>
                </select>
            </div>
            
            <!-- Pilihan Bank (Hanya Muncul Ketika Transfer) -->
            <div id="bankDropdown" class="select-container" style="display: none;">
                <label for="bank" class="data-label">Pilih Bank:</label>
                <select id="bank" name="bank">
                    <option value="" disabled selected>-- Pilih Bank --</option>
                    <option value="BRI SYARIAH">BRI SYARIAH</option>
                    <option value="BSI SYARIAH">BSI SYARIAH</option>
                    <option value="BTN SYARIAH">BTN SYARIAH</option>
                </select>
            </div>

            <!-- Tombol Pesan -->
            <button type="submit" class="payment-btn">Pesan Sekarang</button>
        </form>
    </div>  

    <!-- JavaScript Interaktif -->
    <script>
        const voucherSelect = document.getElementById('voucher');
        const potonganValue = document.getElementById('potongan-value');
        const totalAkhir = document.getElementById('total-akhir');
        const metodeBayarSelect = document.getElementById('metode_bayar');
        const bankDropdown = document.getElementById('bankDropdown');

        // Update potongan voucher dan total akhir
        voucherSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const potongan = parseInt(selectedOption.getAttribute('data-nilai_potongan')) || 0;
            potonganValue.textContent = `Rp. ${potongan.toLocaleString()}`;
            totalAkhir.textContent = `Rp. ${(<?= $total_awal ?> - potongan).toLocaleString()}`;
        });

        // Tampilkan dropdown bank jika metode bayar adalah transfer
        metodeBayarSelect.addEventListener('change', function () {
            if (this.value === 'transfer') {
                bankDropdown.style.display = 'block';
            } else {
                bankDropdown.style.display = 'none';
            }
        });
    </script>
</body>

