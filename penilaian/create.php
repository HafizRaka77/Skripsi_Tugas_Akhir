<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$user_id = $_SESSION['login']["id"];
$alternatives = mysqli_query($connection, "SELECT * FROM alternatives WHERE user_id = '$user_id'");
$criterias = mysqli_query($connection, "SELECT * FROM criterias");

if (isset($_POST['proses'])) {
  $selectedAlternative = $_POST['alternatif']; // Get selected alternative ID
  $criteriasValues = $_POST['criterias']; // Get criteria values as an array

  if (!empty($selectedAlternative) && !empty($criteriasValues)) {
    foreach ($criteriasValues as $criteriaId => $value) {
      $sql = "INSERT INTO alternative_values (alternative_id, criteria_id, value, user_id)
      VALUES ($selectedAlternative, $criteriaId, " . $_POST['weights'][$criteriaId] . ", $user_id)";
      if (mysqli_query($connection, $sql)) {
        $_SESSION['info'] = [
          'status' => 'success',
          'message' => 'Data berhasil ditambahkan'
        ];
      } else {
        $_SESSION['info'] = [
          'status' => 'failed',
          'message' => mysqli_error($connection)
        ];
      }
    }
  } else {
    // Error message if required fields are empty
    echo "<script>alert('Silahkan pilih alternatif dan isikan semua nilai kriteria!');</script>";
  }
}
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Tambah Nilai</h1>
    <a href="./data.php" class="btn btn-light">Kembali</a>
  </div>
  <b><p>PANDUAN PENGISIAN</p></b>
    <p>Skala Penilaian 1-5 dengan ketentuan berikut :
    <p> 1 = Sangat Buruk, 2 = Buruk, 3 = Cukup Baik, 4 = Baik, 5 = Sangat Baik</p>
    <b>Note: Khusus kriteria harga, skala penilaian 1-3 dengan ketentuan berikut : </p></b>
    <b><p> 1 = Mahal, 2 = Normal, 3 = Murah</p></b>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <!-- // Form -->
          <form action="" method="POST">
            <table cellpadding="8" class="w-100">
              <tr>
                <td>
                  <label for="alternatif">Alternatif</label>
                </td>
                <td>
                  <select class="form-control" name="alternatif" id="alternatif">
                    <option value="">Pilih Alternatif</option>
                    <?php
                    while ($data = mysqli_fetch_array($alternatives)) {
                    ?>
                      <option value="<?= $data['id'] ?>"><?= $data['name'] ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="criterias" class="form-label">kriteria:</label>
                </td>
                <td>
                  <?php
                  while ($criteria = mysqli_fetch_assoc($criterias)) :
                  ?>
                    <div class="row">
                      <div class="col-sm-6 mb-2">
                        <input type="hidden" class="form-control" name="criterias[<?= $criteria['id'] ?>]" value="<?= $criteria['id'] ?>">
                        <input type="text" class="form-control" value="<?= $criteria['name'] ?>" disabled>
                      </div>
                      <div class="col-sm-6 mb-2">
                        <input type="number" class="form-control" name="weights[<?= $criteria['id'] ?>]" required max="5" min="1">
                      </div>
                    </div>
                  <?php endwhile; ?>
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

<!-- Page Specific JS File -->
<?php
if (isset($_SESSION['info'])) :
  if ($_SESSION['info']['status'] == 'success') {
?>
    <script>
      iziToast.success({
        title: 'Sukses',
        message: `<?= $_SESSION['info']['message'] ?>`,
        position: 'topCenter',
        timeout: 5000
      });
    </script>
  <?php
  } else {
  ?>
    <script>
      iziToast.error({
        title: 'Gagal',
        message: `<?= $_SESSION['info']['message'] ?>`,
        timeout: 5000,
        position: 'topCenter'
      });
    </script>
<?php
  }

  unset($_SESSION['info']);
  $_SESSION['info'] = null;
endif;
?>