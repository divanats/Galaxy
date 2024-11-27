<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../helper/connection.php';

// Periksa koneksi database
if ($connection->connect_error) {
    die("Koneksi gagal: " . $connection->connect_error);
}

// Ambil data dari form
$id_pemesanan = $_POST['id_pemesanan'] ?? '';
$kode_voucher = $_POST['voucher'] ?? null;
$metode_bayar = $_POST['metode_bayar'] ?? '';
$bank = $_POST['bank'] ?? null;
$total_awal = $_POST['total_awal'] ?? 0; // Total awal sebelum potongan

// Variabel untuk nilai potongan dan total akhir
$id_voucher = null;
$nilai_potongan = 0;
$total_akhir = $total_awal;

// Validasi input
if (empty($id_pemesanan) || empty($metode_bayar)) {
    echo "<script>alert('Harap lengkapi semua data yang dibutuhkan.'); window.history.back();</script>";
    exit;
}

// Cari ID Voucher jika kode voucher tidak kosong
if (!empty($kode_voucher)) {
    $query_voucher = "SELECT id_voucher, nilai_potongan FROM tb_voucher WHERE kode_voucher = ?";
    $stmt_voucher = $connection->prepare($query_voucher);
    $stmt_voucher->bind_param('s', $kode_voucher);
    $stmt_voucher->execute();
    $result_voucher = $stmt_voucher->get_result();

    if ($result_voucher->num_rows > 0) {
        $voucher_data = $result_voucher->fetch_assoc();
        $id_voucher = $voucher_data['id_voucher'];
        $nilai_potongan = $voucher_data['nilai_potongan'];
        $total_akhir = $total_awal - $nilai_potongan; // Hitung total setelah potongan
        if ($total_akhir < 0) {
            $total_akhir = 0; // Pastikan total tidak negatif
        }
    } else {
        echo "<script>alert('Kode voucher tidak valid.'); window.history.back();</script>";
        exit;
    }
}

// Mulai transaksi
$connection->begin_transaction();

try {
    // Update data di `tb_pemesanan_show`
    $query_update_pemesanan = "
        UPDATE tb_pemesanan_show 
        SET id_voucher = ?, nilai_potongan = ?, total_akhir = ?
        WHERE id_pemesanan = ?
    ";
    $stmt_update_pemesanan = $connection->prepare($query_update_pemesanan);
    $stmt_update_pemesanan->bind_param('sdii', $id_voucher, $nilai_potongan, $total_akhir, $id_pemesanan);

    if (!$stmt_update_pemesanan->execute()) {
        throw new Exception("Error pada query update: " . $stmt_update_pemesanan->error);
    }

    // Simpan data ke `tb_pembayaran_show`
    $query_insert_pembayaran = "
        INSERT INTO tb_pembayaran_show (id_pemesanan, metode_bayar, bank, jumlah_bayar) 
        VALUES (?, ?, ?, ?)
    ";
    $stmt_insert_pembayaran = $connection->prepare($query_insert_pembayaran);
    $stmt_insert_pembayaran->bind_param('issd', $id_pemesanan, $metode_bayar, $bank, $total_akhir);

    if (!$stmt_insert_pembayaran->execute()) {
        throw new Exception("Error pada query insert: " . $stmt_insert_pembayaran->error);
    }

    // Commit transaksi
    $connection->commit();

    // Redirect berdasarkan metode bayar
    if ($metode_bayar === 'cash') {
        header("Location: tiket.php?id_pemesanan=$id_pemesanan");
    } elseif ($metode_bayar === 'transfer') {
        header("Location: transfer.php?id_pemesanan=$id_pemesanan");
    } else {
        echo "<script>alert('Metode pembayaran tidak valid.'); window.history.back();</script>";
    }
    exit;
} catch (Exception $e) {
    // Rollback transaksi jika ada kesalahan
    $connection->rollback();
    echo "Kesalahan: " . $e->getMessage();
    exit;
}
?>
