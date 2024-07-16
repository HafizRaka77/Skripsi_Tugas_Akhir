<?php
session_start();
require_once '../helper/connection.php';

$id = $_POST['id'];
$kode = $_POST['kode'];
$nama_alternatif = $_POST['name'];

$query = mysqli_query($connection, "UPDATE alternatives SET kode = '$kode', name = '$nama_alternatif' WHERE kode = '$kode'");
if ($query) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Data berhasil diperbarui'
  ];
  header('Location: ./index.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./index.php');
}
