<?php
require_once 'helper/connection.php';

session_start();
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to fetch user data
    $sql = "SELECT * FROM tb_user WHERE username='$username'";
    $result = mysqli_query($connection, $sql);
    $row = mysqli_fetch_assoc($result);

    // Query to fetch admin data
    $sqlAdmin = "SELECT * FROM tb_admin WHERE username='$username' LIMIT 1";
    $resultAdmin = mysqli_query($connection, $sqlAdmin);
    $adminRow = mysqli_fetch_assoc($resultAdmin);

    // Check if it's a regular user login
    if ((mysqli_num_rows($result) === 1) && ($password === $row['password'])) {
        // Store user data in session, including id_user
        $_SESSION['login'] = $row; // This will include id_user as well
        $_SESSION['id_user'] = $row['id_user']; // Store id_user in session
        header('Location: user/index.php');
        exit;

    // Check if it's an admin login
    } else if ((mysqli_num_rows($resultAdmin) === 1) && ($password === $adminRow['password'])) {
        // Store admin data in session
        $_SESSION['login'] = $adminRow; // This will include admin's info
        $_SESSION['id_user'] = $adminRow['id_user']; // Store id_user in session (if exists in admin table)
        header('Location: admin/index.php');
        exit;

    } else {
        // Set error flag if login fails
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Galaxy Pass</title>
    <!-- Link ke CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h1>Galaxy Pass Login</h1>
        <form action="" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Masukkan username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>

            <?php if (isset($error) && $error): ?>
                <div class="error">Username atau password salah!</div>
            <?php endif; ?>
            
            <button type="submit" name="submit" class="btn">Masuk</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</body>
</html>
