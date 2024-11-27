<?php
require_once '../../helper/connection.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the id_pemesanan from the hidden input
    $id_pemesanan = $_POST['id_pemesanan'];
    
    // Check if the file is uploaded
    if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] === UPLOAD_ERR_OK) {
        // Define the allowed file types and maximum file size (e.g., 2MB)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        // Get the file details
        $file = $_FILES['bukti_bayar'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];

        // Validate the file type
        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('Hanya file JPG, JPEG, atau PNG yang diperbolehkan.'); window.history.back();</script>";
            exit;
        }

        // Validate the file size
        if ($fileSize > $maxFileSize) {
            echo "<script>alert('Ukuran file terlalu besar. Maksimal 2MB.'); window.history.back();</script>";
            exit;
        }

        // Generate a unique name for the file
        $uploadDir = '../../assets/berkas2/';
        $fileNewName = uniqid('', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);

        // Move the uploaded file to the server directory
        if (move_uploaded_file($fileTmpName, $uploadDir . $fileNewName)) {
            // Prepare the query to insert the payment proof into the database
            $query = "UPDATE tb_pembayaran_show SET bukti_bayar = ? WHERE id_pemesanan = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param('si', $fileNewName, $id_pemesanan);

            if ($stmt->execute()) {
                // Success message
                echo "<script>alert('Bukti pembayaran berhasil diunggah.'); window.location.href = 'tiket.php?id_pemesanan=$id_pemesanan';</script>";
            } else {
                // Failure message
                echo "<script>alert('Gagal menyimpan bukti pembayaran.'); window.history.back();</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('Terjadi kesalahan saat mengunggah file.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('File bukti pembayaran tidak ditemukan.'); window.history.back();</script>";
    }
}
?>
