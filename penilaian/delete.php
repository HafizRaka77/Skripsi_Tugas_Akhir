<?php
session_start();
require_once '../helper/connection.php';

$alternative_id = $_GET['alternative_id'];
$user_id = $_SESSION['login']["id"];

$result = mysqli_query($connection, "DELETE FROM alternative_values WHERE alternative_id= $alternative_id AND user_id = '$user_id'");
mysqli_query($connection, "TRUNCATE TABLE alternative_values");

if ($result) {
  $_SESSION['info'] = [
    'status' => 'success',
    'message' => 'Data berhasil dihapus'
  ];
  header('Location: ./data.php');
} else {
  $_SESSION['info'] = [
    'status' => 'failed',
    'message' => mysqli_error($connection)
  ];
  header('Location:./data.php');
}
