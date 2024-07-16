<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$id = $_GET['id'];
$query = mysqli_query($connection, "SELECT * FROM criterias WHERE id='$id'");

$user_id = $_SESSION["login"]['id'];
$item = query_select("criteria_values", ["where" => "user_id = '$user_id' AND criteria_id = '$id'"]);

?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Bobot Kriteria</h1>
    <a href="./index.php" class="btn btn-light">Kembali</a>
  </div>
  <b><p>PANDUAN PENGISIAN</p></b>
  <p>Skala bobot penilaian 1-100 berdasarkan tingkat kepentingan kriteria.<br>
  <b>Note: Setiap kriteria harus memiliki bobot penilaian berbeda. Tidak boleh ada kesamaan bobot penilaian antara kriteria satu dengan lainnya. </p></b>
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
                  <td><input class="form-control" required value="<?= $row['kode'] ?>" disabled></td>
                </tr>
                <tr>
                  <td>Kriteria</td>
                  <td><input class="form-control" type="text" required value="<?= $row['name'] ?>" disabled></td>
                </tr>
                <tr>
                  <td>Bobot</td>
                  <td><input class="form-control" type="number" name="weight" required value="<?= isset($item[0]["weight"]) ? $item[0]["weight"] : "" ?>"required max="100" min="1"></td>
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