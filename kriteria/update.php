<?php
session_start();
require_once '../helper/connection.php';

$id = $_POST['id'];
$kode = $_POST['kode'];
$name = $_POST['name'];
$categories = $_POST['categories'];

$query = mysqli_query($connection, "update criterias set kode='$kode', name='$name', categories='$categories' where id='$id'");
if ($query) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Berhasil mengubah data'
  ];
  header('Location: ./index.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location: ./index.php');
}
