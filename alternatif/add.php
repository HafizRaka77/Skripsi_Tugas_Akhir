<?php
session_start();
require_once '../helper/connection.php';

$kode = $_POST['kode'];
$name = $_POST['name'];

$alternatives = query_select("alternatives");

$list = [];
foreach ($alternatives as $valll) {
  if (!isset($list[$valll["user_id"]])) {
    $list[$valll["user_id"]] = $valll;
  }
}

foreach ($list as $user_id => $value) {
  // code..
  $alternative = [
    "kode" => $kode,
    "name" => $name,
    "nilai_preferensi" => 0,
    "user_id" => $user_id,
  ];

  query_insert("alternatives", $alternative);
}

$query = mysqli_query($connection, "UPDATE alternatives SET nilai_preferensi = 0");

$query = true;

// $query = mysqli_query($connection, "insert into alternatives(kode,name) value('$kode', '$name')");
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
