<?php
require_once 'helper/connection.php'; // Pastikan koneksi sudah benar

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];

    // Check if password and confirm password match
    if ($password === $confirmPassword) {
        // Check if the username is already taken
        $checkQuery = "SELECT * FROM tb_user WHERE username='$username'";
        $resultCheck = mysqli_query($connection, $checkQuery);

        if (mysqli_num_rows($resultCheck) === 0) {
            // Insert new user data into the database
            $insertQuery = "INSERT INTO tb_user (username, password, nama, email, no_hp) 
                            VALUES ('$username', '$password', '$nama', '$email', '$no_hp')";
            $insertResult = mysqli_query($connection, $insertQuery);

            if ($insertResult) {
                // Redirect to login page upon successful registration
                header('Location: login.php');
                exit;
            } else {
                // Redirect back to register form with error message
                header('Location: register_form.php?error=Error during registration.');
                exit;
            }
        } else {
            // Redirect back to register form with error message
            header('Location: register_form.php?error=Username is already taken. Please choose another one.');
            exit;
        }
    } else {
        // Redirect back to register form with error message
        header('Location: register_form.php?error=Passwords do not match.');
        exit;
    }
}
?>
