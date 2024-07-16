<?php
require_once 'helper/connection.php';

if (isset($_POST['submit'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm_password'];
  $role_id = 2; // Default role

  if ($password != $confirmPassword) {
    echo "<script>alert('Password tidak sama!');</script>";
  } else {
    $alternatives = query_select("alternatives");

    $list = [];
    foreach ($alternatives as $valll) {
      if (!isset($list[$valll["kode"]])) {
        $list[$valll["kode"]] = $valll;
      }
    }

    $sql = "INSERT INTO login (username, password, role_id) VALUES ('$username', '$password', '$role_id')";
    if (mysqli_query($connection, $sql)) {
      $user_id = $connection->insert_id;
      foreach ($list as $kode => $value) {
        $newUser = [
          "kode" => $kode,
          "name" => $value["name"],
          "user_id" => $user_id,
          "nilai_preferensi" => 0,
        ];

        query_insert('alternatives', $newUser);
      }

      echo "<script>alert('Berhasil mendaftar!');</script>";
      header('Location: login.php');
    } else {
      echo "<script>alert('Gagal mendaftar!');</script>";
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Register &mdash; SIPECO</title>
  <link rel="shortcut icon" href="assets/icon.png" type="image/png">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.min.css">
</head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
            </div>

            <div class="card card-primary">
              <div class="card-header">
                <h4>Register SIPECO</h4>
              </div>

              <div class="card-body">
                <form method="POST" action="" class="needs-validation" novalidate="">
                  <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" type="text" class="form-control" name="username" tabindex="1" required autofocus>
                    <div class="invalid-feedback">
                      Please enter a username.
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label">Password</label>
                    </div>
                    <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                    <div class="invalid-feedback">
                      Please enter a password.
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                      <label for="confirm_password" class="control-label">Confirm Password</label>
                    </div>
                    <input id="confirm_password" type="password" class="form-control" name="confirm_password" tabindex="3" required>
                    <div class="invalid-feedback">
                      Please confirm your password.
                    </div>
                  </div>

                  <div class="form-group">
                    <button name="submit" type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Register
                    </button>
                  </div>

                  <input type="hidden" name="role" value="1">
                </form>
                <div>
                  <center><a href="login.php">Sudah memiliki akun? Masuk disini</a></center>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>


  <!-- General JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>

  <!-- JS Libraies -->

  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>

  <!-- Page Specific JS File -->
</body>

</html>