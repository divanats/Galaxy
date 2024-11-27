<?php
require_once '../../helper/connection.php';

// Mengecek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $id_pemesanan = $_POST['id_pemesanan'] ?? null;
    $jumlah_bayar = $_POST['jumlah_bayar'] ?? null;
    $metode_bayar = $_POST['metode_bayar'] ?? null;
    $status_bayar = 'belum bayar'; // Status awal adalah belum bayar
    $tgl_bayar = null; // Tanggal bayar kosong jika metode bayar 'cash'
    $bukti_bayar = null; // Tidak ada bukti bayar jika metode bayar 'cash'
    $pilih_bank = $_POST['pilih_bank'] ?? null;

    // Pastikan semua data penting ada
    if (!$id_pemesanan || !$jumlah_bayar || !$metode_bayar) {
        echo '<p>Data yang diperlukan tidak lengkap.</p>';
        exit;
    }

    // Jika metode bayar adalah transfer, kita bisa menyertakan bukti bayar dan tanggal bayar
    if ($metode_bayar === 'transfer') {
        // Simpan data pembayaran sementara dan arahkan ke halaman upload_bukti.php
        header('Location: transfer.php?id_pemesanan=' . $id_pemesanan . '&pilih_bank=' . $pilih_bank);
        exit;
    }

    // Jika metode bayar adalah cash, simpan langsung ke database
    if ($metode_bayar === 'cash') {
        // Query untuk menyimpan data pembayaran ke tb_pembayaran_event
        $sql = "INSERT INTO tb_pembayaran_event (id_pemesanan, jumlah_bayar, metode_bayar, status_bayar, tgl_bayar, bukti_bayar, pilih_bank)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iisssss", $id_pemesanan, $jumlah_bayar, $metode_bayar, $status_bayar, $tgl_bayar, $bukti_bayar, $pilih_bank);

        if ($stmt->execute()) {
            echo '<p>Pembayaran berhasil diproses!</p>';
            header('Location: tiket.php?id_pemesanan=' . $id_pemesanan);
            exit;
        } else {
            echo '<p>Terjadi kesalahan saat memproses pembayaran. Coba lagi nanti.</p>';
        }
    }
} else {
    echo '<p>Permintaan tidak valid.</p>';
}
?>
