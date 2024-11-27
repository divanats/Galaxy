<?php
require_once '../../helper/connection.php';
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['login']['username'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href = '../../login.php';</script>";
    exit();
}

// Ambil data dari form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_show = $_POST['nama_show'];
    $nama_user = $_POST['nama_user'];
    $no_hp = $_POST['no_hp'];
    $nama_instansi = $_POST['nama_instansi'];
    $tgl = $_POST['tgl'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $jmlh_dewasa = (int)$_POST['jmlh_dewasa'];
    $jmlh_anak = (int)$_POST['jmlh_anak'];
    $id_jadwal = (int)$_POST['id_jadwal'];
    $harga_dewasa = (int)$_POST['harga_dewasa'];
    $harga_anak = (int)$_POST['harga_anak'];

    // Ambil username dari session
    $username = $_SESSION['login']['username'];

    $total_awal = ($jmlh_dewasa * $harga_dewasa) + ($jmlh_anak * $harga_anak);

    // Simpan data ke database
    $query = "
        INSERT INTO tb_pemesanan_show (
            nama_show, username, nama_user, no_hp, nama_instansi, tgl, jam_mulai, jam_selesai,
            jmlh_dewasa, jmlh_anak, id_jadwal, total_awal
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = $connection->prepare($query);
    $stmt->bind_param(
        'sssssssiiiii',
        $nama_show, $username, $nama_user, $no_hp, $nama_instansi, $tgl, $jam_mulai,
        $jam_selesai, $jmlh_dewasa, $jmlh_anak, $id_jadwal, $total_awal
    );

    if ($stmt->execute()) {
        $id_pemesanan = $stmt->insert_id;
        header("Location: bayar.php?id_pemesanan=$id_pemesanan");
        exit();
    } else {
        echo "Gagal menyimpan data pemesanan! " . $stmt->error;
    }
}
