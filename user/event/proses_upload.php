<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../../helper/connection.php';

// Mengecek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pemesanan = $_POST['id_pemesanan'];
    $pilih_bank = $_POST['pilih_bank'];
    $bukti_bayar = null;

    // Cek jika ada file bukti pembayaran yang diunggah
    if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] === UPLOAD_ERR_OK) {
        $bukti_bayar = $_FILES['bukti_bayar']['name'];
        $file_extension = pathinfo($bukti_bayar, PATHINFO_EXTENSION);
        
        // Validasi ekstensi file
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            echo '<p>Hanya file dengan ekstensi jpg, jpeg, png, atau pdf yang diperbolehkan.</p>';
            exit;
        }

        // Validasi ukuran file (maksimal 5MB)
        if ($_FILES['bukti_bayar']['size'] > 5000000) { // 5MB
            echo '<p>File terlalu besar. Maksimum ukuran file adalah 5MB.</p>';
            exit;
        }

        // Membuat nama file unik dengan menambahkan timestamp
        $unique_name = uniqid('bukti_') . '.' . $file_extension;
        $upload_path = '../../assets/berkas2/' . $unique_name;

        // Pindahkan file ke direktori tujuan
        if (move_uploaded_file($_FILES['bukti_bayar']['tmp_name'], $upload_path)) {
            // Update status pembayaran menjadi "menunggu konfirmasi"
            $sql = "UPDATE tb_pembayaran_event SET bukti_bayar = ?, status_bayar = 'menunggu konfirmasi' WHERE id_pemesanan = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("si", $unique_name, $id_pemesanan);

            if ($stmt->execute()) {
                // Redirect ke halaman tiket.php dengan ID Pemesanan dan status pembayaran "menunggu konfirmasi"
                header('Location: tiket.php?id_pemesanan=' . $id_pemesanan);
                exit;
            } else {
                echo '<p>Gagal mengupdate status pembayaran di database.</p>';
            }
        } else {
            echo '<p>Terjadi kesalahan saat mengunggah bukti pembayaran.</p>';
        }
    } else {
        echo '<p>File bukti pembayaran tidak ditemukan atau tidak valid.</p>';
    }
}
?>
