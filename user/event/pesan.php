<?php
require_once '../../helper/connection.php';

// Ambil id_event dari URL
$id_event = isset($_GET['id_event']) ? $_GET['id_event'] : null;

// Pastikan id_event ada dan valid
if ($id_event) {
    // Query untuk mengambil data event berdasarkan id_event
    $sql = "SELECT * FROM tb_jadwal_event WHERE id_event = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id_event);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
        
        // Perbaikan path gambar
        $imagePath = !empty($row["gambar_event"]) && file_exists('../../assets/img/' . $row["gambar_event"])
                    ? '../../assets/img/' . $row["gambar_event"]
                    : '../../assets/img/event2.jpeg'; // Gambar default jika tidak ditemukan

        // Hitung kuota tersisa
        $sisa_kuota = $event['kapasitas'] - getBookingsCount($event['id_event']);
    } else {
        echo '<p>No event found with that ID.</p>';
        exit;
    }
} else {
    echo '<p>Invalid event ID.</p>';
    exit;
}

// Fungsi untuk menghitung jumlah pemesanan
function getBookingsCount($id_event) {
    global $connection;
    $sql = "SELECT SUM(jmlh_orang) AS total_orang FROM tb_pemesanan_event WHERE id_event = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id_event);
    $stmt->execute();
    $stmt->bind_result($total_orang);
    $stmt->fetch();
    return $total_orang ? $total_orang : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="booking-page galaxy-theme">
    <h1>Pemesanan Event</h1>
    <div class="booking-container">
        <h2 class="event-name"><?php echo htmlspecialchars($event['nama_event']); ?></h2>
        <div class="event-details">
            <div class="event-image">
                <!-- Menampilkan gambar sesuai event -->
                <img src="<?php echo $imagePath; ?>" alt="Gambar Event">
            </div>
            <div class="form-card">
                <!-- Bagian 1: Deskripsi event -->
                <div class="deskripsi_event">
                    <?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?>
                </div>

                <!-- Bagian 2: Tanggal, Jam, Kuota, dan Form Pemesanan -->
                <div class="event-info">
                    <p><strong>Tanggal:</strong> <?php echo date("d M Y", strtotime($event['tgl'])); ?></p>
                    <p><strong>Jam:</strong> <?php echo date("H:i", strtotime($event['jam_mulai'])); ?> - <?php echo (!empty($event['jam_selesai']) ? date("H:i", strtotime($event['jam_selesai'])) : "Selesai"); ?></p>
                    <p><strong>Harga per orang:</strong> Rp<?php echo number_format($event['harga'], 0, ',', '.'); ?></p>
                    <p><strong>Sisa Kuota:</strong> <?php echo $sisa_kuota; ?></p>

                    <!-- Form Pemesanan -->
                    <form method="POST" action="store.php?id_event=<?php echo $id_event; ?>" id="booking-form">
                        <label for="nama_user">Nama Pemesan</label>
                        <input type="text" id="nama_user" name="nama_user" required>

                        <label for="no_hp">No HP</label>
                        <input type="text" id="no_hp" name="no_hp" required>

                        <label for="jmlh_orang">Jumlah Orang</label>
                        <input type="number" id="jmlh_orang" name="jmlh_orang" min="1" max="<?php echo $sisa_kuota; ?>" required>
                        <input type="hidden" name="username" value="<?= htmlspecialchars($_SESSION['login']['username']) ?>">
                    </form>
                    <div class="button-container">
                            <button type="submit" form="booking-form">Pesan Sekarang</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
