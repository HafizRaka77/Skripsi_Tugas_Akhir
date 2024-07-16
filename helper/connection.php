<?php
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "spk_toko_online";

$connection = mysqli_connect($dbhost, $dbusername, $dbpassword,  $dbname);

use function PHPSTORM_META\type;

function query_select($table, $role = [])
{
  global $connection;
  $result = null;
  $sql = "SELECT * FROM $table";

  if (isset($role['join'])) {
    $sql .= " JOIN " . $role['join'];
  }

  if (isset($role['where'])) {
    $sql .= " WHERE " . $role['where'];
  }

  if (isset($role['orderby'])) {
    $sql .= " ORDER BY " . $role['orderby'];
  }

  if (isset($role['limit'])) {
    $sql .= " LIMIT " . $role['limit'];
  }

  $result = mysqli_query($connection, $sql);

  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }

  return $rows;
}

function query_find($table, $role = null)
{
  global $connection;
  $result = null;
  $sql = "SELECT * FROM $table";

  if ($role) {
    $sql .= " WHERE " . $role;
  }

  $result = mysqli_query($connection, $sql);

  $data = null;
  while ($row = mysqli_fetch_assoc($result)) {
    $data = $row;
  }

  return $data;
}

function query_insert($table, $data)
{
  global $connection;

  $colum = "";
  $value = "";
  $i = 1;
  foreach ($data as $col => $val) {
    $colum .= $col;
    $value .= "'" . $val . "'";
    if ($i != count($data)) {
      $value .= ", ";
      $colum .= ", ";
    }
    $i++;
  }
  unset($i);
  // echo "INSERT INTO $table ($colum) VALUES($value)";
  mysqli_query($connection, "INSERT INTO $table ($colum) VALUES($value)");
  return mysqli_affected_rows($connection);
}

function query_update($table, $data, $where)
{
  global $connection;

  $set = '';
  $i = 1;
  foreach ($data as $col => $val) {
    $set .= $col . " = '" . $val . "' ";
    if ($i != count($data)) {
      $set .= ", ";
    }

    $i++;
  }
  unset($i);

  $sql = "UPDATE $table SET $set WHERE $where";

  mysqli_query($connection, $sql);
  return mysqli_affected_rows($connection);
}

function query_delete($table, $where)
{
  global $connection;

  mysqli_query($connection, "DELETE FROM $table WHERE $where");
  return mysqli_affected_rows($connection);
}

function query_count($table, $role = [])
{
  global $connection;
  $result = null;
  $sql = "SELECT * FROM $table";

  if (isset($role['where'])) {
    $sql .= " WHERE " . $role['where'];
  }

  $result = mysqli_query($connection, $sql);

  return $result->num_rows;
}

function arrayWhere($table, $key, $role = [])
{
  global $connection;
  $result = null;
  $sql = "SELECT * FROM $table";

  if (isset($role['join'])) {
    $sql .= " JOIN " . $role['join'];
  }

  if (isset($role['where'])) {
    $sql .= " WHERE " . $role['where'];
  }

  if (isset($role['orderby'])) {
    $sql .= " ORDER BY " . $role['orderby'];
  }

  if (isset($role['limit'])) {
    $sql .= " LIMIT " . $role['limit'];
  }

  $result = mysqli_query($connection, $sql);
  $data = $result;

  $arrayWhere = [];

  foreach ($data as $i => $value) {
    $arrayWhere[$value[$key]][] = $value;
  }

  return $arrayWhere;
}

