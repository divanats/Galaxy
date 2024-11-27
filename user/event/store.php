<?php
require_once '../../helper/connection.php';
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['login']['username'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href = '../../login.php';</script>";
    exit();
}
// Mengecek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_user = $_POST['nama_user'];
    $no_hp = $_POST['no_hp'];
    $jmlh_orang = $_POST['jmlh_orang'];
    $id_event = $_GET['id_event']; // Ambil id_event dari URL

    // Ambil username dari session
    $username = $_SESSION['login']['username'];

    // Query untuk mengambil data event berdasarkan id_event
    $sql_event = "SELECT * FROM tb_jadwal_event WHERE id_event = ?";
    $stmt_event = $connection->prepare($sql_event);
    $stmt_event->bind_param("i", $id_event);
    $stmt_event->execute();
    $result_event = $stmt_event->get_result();
    
    if ($result_event->num_rows > 0) {
        $event = $result_event->fetch_assoc();
        
        // Menyimpan nama event, tanggal, jam, dan total harga
        $nama_event = $event['nama_event'];
        $tgl_event = $event['tgl']; // Tanggal event
        $jam_mulai = $event['jam_mulai']; // Jam mulai event
        $jam_selesai = $event['jam_selesai'] ? $event['jam_selesai'] : NULL; // Tangani NULL
        $harga = $event['harga']; // Harga per orang

        // Hitung total harga
        $total_harga = $harga * $jmlh_orang;

        // Query untuk menyimpan pemesanan ke database
        $sql = "INSERT INTO tb_pemesanan_event (nama_event, username, tgl, jam_mulai, jam_selesai, nama_user, no_hp, jmlh_orang, total_harga, id_event) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Perhatikan jumlah parameter bind yang sesuai dengan query
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssssssiii", $nama_event, $username, $tgl_event, $jam_mulai, $jam_selesai, $nama_user, $no_hp, $jmlh_orang, $total_harga, $id_event);

        if ($stmt->execute()) {
            // Mendapatkan id pemesanan yang baru saja disimpan
            $id_pemesanan = $stmt->insert_id;

            // Redirect ke halaman pembayaran dengan id_pemesanan sebagai parameter
            header("Location: bayar.php?id_pemesanan=$id_pemesanan");
            exit; // Pastikan script berhenti setelah redirect
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Event tidak ditemukan.";
    }
}
?>
