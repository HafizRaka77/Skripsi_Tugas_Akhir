<?php
session_start();
require_once '../helper/connection.php';

$kode = $_POST['kode'];
$nama = $_POST['name'];
$categories = $_POST['categories'];

$query = mysqli_query($connection, "insert into criterias (kode, name, categories) value('$kode', '$nama', '$categories')");
if ($query) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Data berhasil ditambah'
  ];
  header('Location: ./index.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./index.php');
}
