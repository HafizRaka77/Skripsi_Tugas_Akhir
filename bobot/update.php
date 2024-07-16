<?php
session_start();
require_once '../helper/connection.php';

$id = $_POST['id'];
$weight = $_POST['weight'];
$user_id = $_SESSION["login"]['id'];

query_delete("criteria_values", "user_id = '$user_id' AND criteria_id = '$id'");
query_insert("criteria_values", [
  "user_id" => $user_id,
  "criteria_id" => $id,
  "weight" => $weight,
]);

$_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Data berhasil diperbarui'
  ];
header('Location: ./index.php');

die;

// $query = mysqli_query($connection, "UPDATE criterias SET weight = '$weight' WHERE id = '$id'");
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
