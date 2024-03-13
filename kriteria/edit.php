<?php
ob_start(); // Turn on output buffering
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$id = $_GET['id'];
$query = mysqli_query($connection, "SELECT * FROM criterias WHERE id='$id'");

// Check role and redirect if needed
if ($role != 1) {
  header("Location: ../dashboard/index.php");
  exit;
}

ob_end_flush(); // Flush buffered content
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Ubah Kriteria</h1>
    <a href="./index.php" class="btn btn-light">Kembali</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <!-- // Form -->
          <form action="./update.php" method="post">
            <?php
            while ($row = mysqli_fetch_array($query)) {
            ?>
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <table cellpadding="8" class="w-100">
                <tr>
                  <td>ID</td>
                  <td><input class="form-control" required value="<?= $row['id'] ?>" disabled></td>
                </tr>
                <tr>
                  <td>Kode</td>
                  <td><input class="form-control" type="text" name="kode" required value="<?= $row['kode'] ?>" disabled></td>
                </tr>
                <tr>
                  <td>Nama Kriteria</td>
                  <td><input class="form-control" type="text" name="name" required value="<?= $row['name'] ?>"></td>
                </tr>
                <tr>
                  <td>Kategori</td>
                  <td>
                    <select class="form-control" name="categories" id="categories" required>
                      <option value="">--Pilih Kategori--</option>
                      <option value="benefit" <?= $row['categories'] == 'benefit' ? 'selected' : '' ?>>Benefit</option>
                      <option value="cost" <?= $row['categories'] == 'cost' ? 'selected' : '' ?>>Cost</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input class="btn btn-primary d-inline" type="submit" name="proses" value="Ubah">
                    <a href="./index.php" class="btn btn-danger ml-1">Batal</a>
                  <td>
                </tr>
              </table>

            <?php } ?>
          </form>
        </div>
      </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>