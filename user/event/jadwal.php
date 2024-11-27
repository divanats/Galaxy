<?php
require_once '../../helper/connection.php';

// Fungsi untuk menghitung total jumlah orang yang sudah memesan
function getBookingsCount($id_event) {
    global $connection;
    $sql = "SELECT SUM(jmlh_orang) AS total_orang FROM tb_pemesanan_event WHERE id_event = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id_event);
    $stmt->execute();
    $stmt->bind_result($total_orang);
    $stmt->fetch();
    return $total_orang ? $total_orang : 0; // Jika null, kembalikan 0
}

// Query untuk mengambil data event
$sql = "SELECT * FROM tb_jadwal_event";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event List</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="galaxy-theme">
    <h1>Upcoming Events</h1>
    <div class="event-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Hitung kuota yang tersisa
                $total_dipesan = getBookingsCount($row['id_event']);
                $sisa_kuota = $row['kapasitas'] - $total_dipesan;

                // Ambil data gambar dari database, pastikan path gambar sesuai dengan yang ada
                $imagePath = !empty($row["gambar_event"]) && file_exists('../../assets/img/' . $row["gambar_event"])
                    ? '../../assets/img/' . $row["gambar_event"]
                    : '../../assets/img/event2.jpeg'; // Default image jika tidak ada gambar

                echo '
                <div class="event-card">
                    <div class="image-wrapper">
                        <img src="' . $imagePath . '" alt="' . htmlspecialchars($row["nama_event"]) . '">
                    </div>
                    <div class="event-content">
                        <a href="pesan.php?id_event=' . $row["id_event"] . '" class="event-button">
                            Pilih
                        </a>
                        <h2>' . htmlspecialchars($row["nama_event"]) . '</h2>
                        <div class="event-details">
                            <p><strong>Hari</strong><br>' . date("d M Y", strtotime($row["tgl"])) . '</p>
                            <p><strong>Kuota Tersisa</strong><br>' . $sisa_kuota . ' orang</p>
                        </div>
                        <p><strong>Jam</strong><br>' . date("H:i", strtotime($row["jam_mulai"])) . ' - ' . (!empty($row["jam_selesai"]) ? date("H:i", strtotime($row["jam_selesai"])) : "Selesai") . ' WIB</p>
                    </div>
                </div>
                ';
            }
        } else {
            echo '<p>Tidak ada event tersedia.</p>';
        }
        ?>
    </div>
</body>
</html>
