<?php

function getUserRole($connection)
{
  // Check if user ID is available in session
  if (!isset($_SESSION['login']['id'])) {
    return null; // No user ID, return null
  }

  $userId = (int) $_SESSION['login']['id'];

  // Use prepared statement for security
  $sql = "SELECT role_id FROM login WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_bind_param($stmt, "i", $userId);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  // Check if query was successful
  if (!$result) {
    // Handle error: e.g., log error and return null
    return null;
  }

  // Fetch and return role if available
  if ($row = mysqli_fetch_assoc($result)) {
    return $row['role_id'];
  } else {
    return null; // User ID not found in database
  }
}
