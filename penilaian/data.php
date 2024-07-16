<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$user_id = $_SESSION['login']["id"];

$criteria_query = mysqli_query($connection, "SELECT * FROM criterias");
$alternatif_query = mysqli_query($connection, "SELECT * FROM alternatives WHERE user_id = '$user_id'");
$alternative_values_query = mysqli_query($connection, "SELECT * FROM alternative_values WHERE user_id = '$user_id'");

// Fetch data into an array
$criterias = mysqli_fetch_all($criteria_query, MYSQLI_ASSOC);
$alternatives = mysqli_fetch_all($alternatif_query, MYSQLI_ASSOC);
$alternative_values = mysqli_fetch_all($alternative_values_query, MYSQLI_ASSOC);

?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Data Penilaian</h1>
    <a href="./create.php" class="btn btn-primary">Input Penilaian</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="table-1">
              <thead>
                <tr class="text-center">
                  <th>Kode</th>
                  <?php foreach ($criterias as $kriteria) : ?>
                    <th><?= $kriteria['kode'] ?></th>
                  <?php endforeach; ?>
                  <th style="width: 150">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($alternatives as $alternatif) : ?>
                  <tr class="text-center">
                    <td><?= $alternatif['kode'] ?></td>
                    <?php 
                    $cek = true;
                     ?>
                    <?php foreach ($criterias as $kriteria) : ?>
                      <td>
                        <?php
                        $alternatif_value = null;
                        foreach ($alternative_values as $value) {
                          if ($value['alternative_id'] == $alternatif['id'] && $value['criteria_id'] == $kriteria['id']) {
                            $alternatif_value = $value['value'];
                            break;
                          }
                        }
                        if ($alternatif_value !== null) {
                          echo '<h6>' . $alternatif_value . '</h6>';
                        } else {
                          $cek = false;
                          echo '<h6>-</h6>';
                        }
                        ?>
                      </td>
                    <?php endforeach; ?>
                    <td>
                      <?php if ($cek): ?>
                      <a class="btn btn-sm btn-info" href="edit.php?id=<?= $alternatif['id'] ?>">
                        <i class="fas fa-edit fa-fw"></i>
                      </a>
                      <?php endif ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <a class="btn btn-danger mb-md-0 mb-1" href="./delete.php?alternative_id=<?= $alternatif['id'] ?>" role="button" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')" class="btn btn-primary">Delete Data</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>

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