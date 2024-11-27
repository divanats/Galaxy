<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Galaxy Pass</title>
    <!-- Link ke CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
</head>
<body class="dashboard-page">
    <!-- Navigasi -->
    <nav class="dashboard-page">
        <div class="logo">
            <h2>Galaxy Pass</h2>
        </div>
        <ul class="nav-dashboard-page-links">
            <li class="nav-dashboard"><a href="#">Home</a></li>
            <li class="nav-dashboard"><a href="#informasi">Informasi</a></li>
            <li class="nav-dashboard"><a href="../show/jadwal.php">Jadwal Show</a></li>
            <li class="nav-dashboard"><a href="../event/jadwal.php">Jadwal Event</a></li>
            <li class="nav-dashboard"><a href="../riwayat/index.php">Riwayat Saya</a></li>
            <li class="nav-dashboard"><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Konten Dashboard -->
    <div class="dashboard-container">
        <div class="welcome-message">
            <h1>Selamat Datang, <?php echo $_SESSION['login']['username']; ?>!</h1>
            <p>Ayo, mulailah petualangan luar angkasa yang tak terlupakan dengan Galaxy Pass! Waktunya menjelajahi dunia baru.</p>
        </div>

        <!-- Card untuk Info Tata Tertib -->
        <div class="card-dashboard-container" id="informasi">
                    <div class="card-dashboard">
                        <h3>Tata Tertib</h3>
                        <p>Berikut adalah beberapa tata tertib yang harus diperhatikan saat mengunjungi Planetarium:</p>
                        <ol style="list-style-position: inside;">
                            <li>Pengunjung diharapkan datang tepat waktu sesuai jadwal show.</li>
                            <li>Harap menjaga ketenangan selama pertunjukan untuk kenyamanan bersama.</li>
                            <li>Pengunjung dilarang membawa makanan dan minuman ke dalam ruang pertunjukan.</li>
                            <li>Selalu ikuti petunjuk dari petugas untuk keamanan selama kunjungan.</li>
                            <li>Pengunjung yang membawa anak-anak di bawah usia 12 tahun harus didampingi oleh orang dewasa.</li>
                        </ol>
                    </div>
                    <div class="card-dashboard">
                        <h3>Tata Cara Pemesanan Tiket</h3>
                        <p>Untuk memesan tiket, ikuti langkah-langkah berikut:</p>
                        <ol style="list-style-position: inside;">
                            <li>Buka website resmi planetarium di <strong>www.planetarium.com</strong>.</li>
                            <li>Masuk atau daftar akun baru jika belum memiliki akun.</li>
                            <li>Pilih acara atau show yang ingin dikunjungi pada halaman jadwal.</li>
                            <li>Sesuaikan jumlah tiket yang ingin dipesan.</li>
                            <li>Isi informasi pengunjung dan pilih metode pembayaran yang tersedia.</li>
                            <li>Selesaikan pembayaran dan tiket akan dikirimkan melalui email atau SMS.</li>
                            <li>Perlihatkan tiket yang sudah diterima saat memasuki area planetarium.</li>
                        </ol>
            </div>
        </div>

    <!-- Animasi roket -->
    <div class="rocket-container">
        <div class="rocket"></div>
    </div>
    <!-- Animasi stars -->
    <div class="stars-container">
        <div class="stars"></div>
        <div class="stars"></div>
        <div class="stars"></div>
        <div class="stars"></div>
        <div class="stars"></div>
        <div class="stars"></div>
    </div>

     <!-- Footer Section -->
     <footer>
        <p>&copy; 2024 Galaxy Pass. All Rights Reserved.</p>
    </footer>
    
</body>
</html>
