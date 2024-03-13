<?php
ob_start(); // Start output buffering

// Include files that might send headers
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Check role and redirect if needed
if ($role != 1) {
  header("Location: ../dashboard/index.php");
  exit;
}

ob_end_flush(); // Flush buffered content
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Tambah Kriteria</h1>
    <a href="./index.php" class="btn btn-light">Kembali</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <!-- // Form -->
          <form action="./add.php" method="POST">
            <table cellpadding="8" class="w-100">
              <tr>
                <td>Kode</td>
                <td><input class="form-control" type="text" name="kode"></td>
              </tr>
              <tr>
                <td>Nama Kriteria</td>
                <td><input class="form-control" type="text" name="name"></td>
              </tr>
              <tr>
                <td>Kategori</td>
                <td>
                  <select class="form-control" name="categories" id="categories" required>
                    <option value="">--Pilih Kategori--</option>
                    <option value="benefit">Benefit</option>
                    <option value="cost">Cost</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  <input class="btn btn-primary" type="submit" name="proses" value="Simpan">
                  <input class="btn btn-danger" type="reset" name="batal" value="Bersihkan">
                </td>
              </tr>
            </table>
          </form>
        </div>
      </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>