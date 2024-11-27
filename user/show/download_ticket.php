<?php
// Mulai output buffering
ob_start();

// Pastikan file autoload Dompdf ter-load
require_once '../../helper/connection.php'; // Atur path koneksi database Anda dengan benar
require '../../vendor/autoload.php'; // Pastikan path ke autoload benar

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil ID pemesanan dari parameter URL
$id_pemesanan = $_GET['id'];

// Koneksi database
$query = "SELECT 
                p.nama_user, 
                p.tgl, 
                p.nama_instansi, 
                p.nama_show, 
                p.jam_mulai, 
                p.jam_selesai, 
                p.jmlh_dewasa, 
                p.jmlh_anak, 
                p.total_akhir, 
                b.metode_bayar
          FROM 
                tb_pemesanan_show p 
          JOIN 
                tb_pembayaran_show b ON p.id_pemesanan = b.id_pemesanan
          WHERE 
                p.id_pemesanan = ?";

$stmt = $connection->prepare($query);
$stmt->bind_param("i", $id_pemesanan);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Jika data pemesanan tidak ditemukan
if (!$order) {
    die("Tiket tidak ditemukan!");
}

// HTML untuk PDF dengan inline CSS
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - GalaxyPass</title>
    <style>
    margin-bottom: 10px;
}

.barcode-section svg {
    max-width: 100%;
    height: auto;
    margin-top: 10px;
} bisa ringkesin gak?
ChatGPT said:
ChatGPT
Tentu! Berikut adalah versi ringkas dari CSS yang Anda berikan dengan mengurangi duplikasi dan menyusun aturan dengan lebih efisien:

css
Copy code
/* Global Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    color: #333;
    margin: 0;
    padding: 0;
}

/* Tiket Container */
.tiket-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

/* Tiket Card */
.ticket-card {
    width: 80%;
    max-width: 900px;
    background-color: #2e4b7c;
    color: white;
    display: flex;
    flex-direction: column;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding-bottom: 50px;
    margin: auto;
    position: relative;
}

/* Header Section */
.ticket-header {
    display: flex;
    justify-content: space-between;
    padding: 15px;
    background-color: #4a90e2;
    color: white;
}

.ticket-logo {
    width: 40px;
    height: auto;
    margin-right: 10px;
}

/* Watermark */
.watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 100px;
    color: rgba(255, 255, 255, 0.15);
    font-weight: bold;
    letter-spacing: 10px;
    text-transform: uppercase;
    z-index: 0;
}

/* Ticket Content */
.ticket-content {
    display: flex;
    flex-direction: column;
    padding: 20px;
    gap: 20px;
}

/* Info Section */
.ticket-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
    font-size: 16px;
}

.ticket-info p {
    margin: 0;
    display: flex;
    justify-content: space-between;
    font-weight: 400;
}

.ticket-info strong {
    color: #f5a623;
    font-weight: 500;
}

/* Barcode Section */
.barcode-section {
    text-align: center;
    padding: 10px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.barcode-section h4 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
    color: #f5a623;
}

.barcode svg {
    max-width: 100%;
    height: 80px;
    margin-top: 10px;
}

/* Layout untuk Info dan Barcode */
.ticket-row {
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.ticket-info p {
    gap: 10px;
}

.ticket-info strong {
    min-width: 150px;
}

.info-value {
    text-align: left;
}

/* Wrapper untuk Barcode dan Garis Vertikal */
.barcode-section-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
}

    </style>
</head>
<body>
    <section class="section">
        <div class="section-header">
            <h1>Galaxy Pass</h1>
        </div>
    </section>
    <section class="tiket-container">
        <div class="ticket-card">
            <!-- Header Card hanya berisi logo -->
            <div class="ticket-header">
                <div class="header-left">
                    <img src="../../assets/img/logoku.png" alt="Logo" class="ticket-logo">
                </div>
                <div class="header-right">
                    <h1>Galaxy Pass</h1>
                </div>
            </div>

            <!-- Ticket Content -->
            <div class="ticket-content">
                <div class="ticket-info">
                    <p><strong>Nama Pemesan:</strong> ' . htmlspecialchars($order['nama_user']) . '</p>
                    <p><strong>Tanggal:</strong> ' . htmlspecialchars($order['tgl']) . '</p>
                    <p><strong>Nama Instansi:</strong> ' . htmlspecialchars($order['nama_instansi']) . '</p>
                    <p><strong>Sesi:</strong> ' . htmlspecialchars($order['nama_show']) . '</p>
                    <p><strong>Jam:</strong> ' . htmlspecialchars($order['jam_mulai']) . ' - ' . htmlspecialchars($order['jam_selesai']) . '</p>
                    <p><strong>Jumlah Dewasa:</strong> ' . htmlspecialchars($order['jmlh_dewasa']) . '</p>
                    <p><strong>Jumlah Anak:</strong> ' . htmlspecialchars($order['jmlh_anak']) . '</p>
                    <p><strong>Total Bayar:</strong> Rp ' . number_format($order['total_akhir'], 0, ',', '.') . '</p>
                    <p><strong>Metode Pembayaran:</strong> ' . htmlspecialchars($order['metode_bayar']) . '</p>
                </div>
                <div class="barcode-section-wrapper">
                    <div class="vertical-line"></div>
                    <div class="barcode-section">
                        <h4 class="highlight">Kode Pemesanan</h4>
                        <svg id="barcode"></svg>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>';

// Inisialisasi Dompdf dengan pengaturan
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Izinkan akses ke file eksternal

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Bersihkan buffer output sebelum mengirim PDF
ob_end_clean();

// Kirim file PDF ke browser
$dompdf->stream("Tiket_GalaxyPass.pdf", ["Attachment" => true]);

// Keluar dari script
exit();
