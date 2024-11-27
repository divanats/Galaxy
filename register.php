<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Galaxy Pass</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h1>Galaxy Pass - Register</h1>
        <form action="proses_register.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Masukkan username" required>

            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>

            <label for="email">Email</label>
            <input type="text" id="email" name="email" placeholder="Masukkan email" required>

            <label for="no_hp">Nomor HP</label>
            <input type="text" id="no_hp" name="no_hp" placeholder="Masukkan nomor HP" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>

            <label for="confirmPassword">Konfirmasi Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Konfirmasi password" required>

            <?php if (isset($_GET['error'])): ?>
                <div class="error"><?php echo $_GET['error']; ?></div>
            <?php endif; ?>

            <button type="submit" name="register" class="btn">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
    </div>
</body>
</html>
