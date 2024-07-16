<?php
session_start();
require_once '../helper/connection.php';

$id = $_GET['id'];

$allAlter = query_select("alternatives", [
  "where" => "kode = '$id'",
]);

foreach ($allAlter as $item) {

  $alt_id = $item["id"];
  $result = mysqli_query($connection, "DELETE FROM alternatives WHERE id = '$alt_id'");
  mysqli_query($connection, "TRUNCATE TABLE alternative_values");

}

if ($result) {
  
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Data berhasil dihapus'
  ];

  header('Location: ./index.php');

} else {

  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];

  header('Location: ./index.php');
}
