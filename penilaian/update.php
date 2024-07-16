<?php
session_start();
require_once '../helper/connection.php';

$id = $_POST['id']; // Mendapatkan ID alternatif dari formulir
$user_id = $_SESSION['login']["id"];


// Ambil data input dari formulir
$kode = $_POST['kode'];
$nama = $_POST['nama'];
$nilai = $_POST['nilai'];



// Update data alternatif
$query_alternative = "UPDATE alternatives SET kode='$kode', name='$nama' WHERE id='$id' AND user_id = '$user_id'";
$result_alternative = mysqli_query($connection, $query_alternative);

// Update nilai alternatif
foreach ($nilai as $criteria_id => $value) {
  $query_value = "UPDATE alternative_values SET value='$value' WHERE alternative_id='$id' AND criteria_id='$criteria_id' AND user_id = '$user_id'";
  $result_value = mysqli_query($connection, $query_value);
}

// Cek apakah proses update berhasil atau tidak
if ($result_alternative && $result_value) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Data berhasil diperbarui'
  ];
  header('Location: ./data.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./data.php');
}
