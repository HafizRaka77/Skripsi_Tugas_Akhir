<?php
session_start();
require_once '../helper/connection.php';

$id = $_POST['id'];
$weight = $_POST['weight'];

$query = mysqli_query($connection, "UPDATE criterias SET weight = '$weight' WHERE id = '$id'");
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
