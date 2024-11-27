<?php
session_start();
require_once '../../helper/connection.php'; // Pastikan path ini benar

// Cek apakah user sudah login
if (!isset($_SESSION['login'])) {
    header("Location: ../../login.php");
    exit;
}

// Ambil username dari sesi login
$username = $_SESSION['login']['username'];

// Query untuk mendapatkan data riwayat pemesanan tiket show
$queryShow = "SELECT 
                tb_pemesanan_show.id_pemesanan,
                tb_pemesanan_show.nama_show, 
                tb_pemesanan_show.nama_user, 
                tb_pemesanan_show.no_hp,
                tb_pemesanan_show.nama_instansi,
                tb_pemesanan_show.tgl,
                tb_pemesanan_show.jam_mulai, 
                tb_pemesanan_show.jam_selesai, 
                tb_pemesanan_show.jmlh_anak, 
                tb_pemesanan_show.jmlh_dewasa,
                tb_pembayaran_show.jumlah_bayar,
                tb_pembayaran_show.metode_bayar
              FROM tb_pemesanan_show
              LEFT JOIN tb_pembayaran_show 
              ON tb_pemesanan_show.id_pemesanan = tb_pembayaran_show.id_pemesanan
              WHERE tb_pemesanan_show.username = ?";
$stmtShow = $connection->prepare($queryShow);
$stmtShow->bind_param("s", $username);
$stmtShow->execute();
$resultShow = $stmtShow->get_result();

// Query untuk mendapatkan data riwayat event
$queryEvent = "SELECT 
                tb_pemesanan_event.id_pemesanan,
                tb_pemesanan_event.nama_user,
                tb_pemesanan_event.no_hp,
                tb_pemesanan_event.tgl,
                tb_pemesanan_event.jam_mulai, 
                tb_pemesanan_event.jam_selesai,
                tb_pemesanan_event.jmlh_orang,
                tb_pemesanan_event.total_harga,
                tb_pembayaran_event.metode_bayar
              FROM tb_pemesanan_event
              LEFT JOIN tb_pembayaran_event 
              ON tb_pemesanan_event.id_pemesanan = tb_pembayaran_event.id_pemesanan
              WHERE tb_pemesanan_event.username = ?";
$stmtEvent = $connection->prepare($queryEvent);
$stmtEvent->bind_param("s", $username);
$stmtEvent->execute();
$resultEvent = $stmtEvent->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Riwayat Pemesanan Tiket Show dan Event</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/css/riwayat.css">
  
</head>

<body class="galaxy-theme">
    <section class="galaxy-container mt-5">
        <h1 class="galaxy-title">Daftar Pemesanan Tiket Show</h1>
        <table class="galaxy-table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pemesanan</th>
                    <th>Tanggal Pemesanan</th>
                    <th>Nama Pemesan</th>
                    <th>No HP</th>
                    <th>Nama Instansi</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Jumlah Anak</th>
                    <th>Jumlah Dewasa</th>
                    <th>Jumlah Bayar</th>
                    <th>Metode Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($resultShow) > 0) : ?>
                    <?php $no = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($resultShow)) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['id_pemesanan'] ?></td>
                            <td><?= $row['tgl'] ?></td>
                            <td><?= $row['nama_user'] ?></td>
                            <td><?= $row['no_hp'] ?></td>
                            <td><?= $row['nama_instansi'] ?></td>
                            <td><?= $row['jam_mulai'] ?></td>
                            <td><?= $row['jam_selesai'] ?></td>
                            <td><?= $row['jmlh_anak'] ?></td>
                            <td><?= $row['jmlh_dewasa'] ?></td>
                            <td><?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                            <td><?= $row['metode_bayar'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="12" class="text-center">Data tidak ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h1 class="galaxy-title">Daftar Pemesanan Tiket Event</h1>
        <table class="galaxy-table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pemesanan</th>
                    <th>Tanggal Pemesanan</th>
                    <th>Nama Pemesan</th>
                    <th>No HP</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Jumlah Orang</th>
                    <th>Jumlah Bayar</th>
                    <th>Metode Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($resultEvent) > 0) : ?>
                    <?php $no = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($resultEvent)) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['id_pemesanan'] ?></td>
                            <td><?= $row['tgl'] ?></td>
                            <td><?= $row['nama_user'] ?></td>
                            <td><?= $row['no_hp'] ?></td>
                            <td><?= $row['jam_mulai'] ?></td>
                            <td><?= $row['jam_selesai'] ?></td>
                            <td><?= $row['jmlh_orang'] ?></td>
                            <td><?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td><?= $row['metode_bayar'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="10" class="text-center">Data tidak ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</body>

</html>
