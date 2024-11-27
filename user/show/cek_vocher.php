<?php
require_once 'helper/connection.php';  // Ganti dengan path ke file koneksi database kamu

// Mengambil kode voucher dari request
$voucher_code = isset($_POST['voucher']) ? $_POST['voucher'] : '';

// Query untuk memeriksa voucher
$query = "SELECT * FROM tb_vocher WHERE kode_vocher = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('s', $voucher_code);
$stmt->execute();
$result = $stmt->get_result();

$response = [];

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $response['status'] = 'valid';
    $response['potongan'] = $data['nilai_potongan'];  // Nilai potongan dari voucher
} else {
    $response['status'] = 'invalid';
    $response['message'] = 'Voucher tidak valid';
}

echo json_encode($response);  // Mengirimkan respons dalam format JSON
?>