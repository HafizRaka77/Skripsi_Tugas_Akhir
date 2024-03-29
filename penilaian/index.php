<?php
ob_start(); // Turn on output buffering
require_once '../layout/_top.php';
require_once '../helper/connection.php';
require_once '../helper/topsis.php';

$countAlternativesValue = mysqli_query($connection, "SELECT COUNT(*) as total FROM alternative_values");
$countAlternatives = mysqli_query($connection, "SELECT COUNT(*) as total FROM alternatives");
$countAlternatives = mysqli_fetch_assoc($countAlternatives);
$countCriterias = mysqli_query($connection, "SELECT COUNT(*) as total FROM criterias");
$countCriterias = mysqli_fetch_assoc($countCriterias);

$countAlternativesCriterias = $countAlternatives['total'] * $countCriterias['total'];

if ($countAlternativesValue) {
  $count = mysqli_fetch_assoc($countAlternativesValue);
  if ($count['total'] === $countAlternativesCriterias) {
    $_SESSION['info'] = [
      'status' => 'failed',
      'message' => 'Silahkan input semua alternatif dan kriteria terlebih dahulu!'
    ];
    header('Location: ./create.php');
    exit;
  }
}

$result = getTopsisResult();

$sql = "SELECT * FROM alternatives ORDER BY nilai_preferensi DESC";
$result = mysqli_query($connection, $sql);

ob_end_flush(); // Flush buffered content
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Ranking</h1>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="table-1">
              <thead>
                <tr class="text-center">
                  <th>Ranking</th>
                  <th>Kode</th>
                  <th>Nama Alternatif</th>
                  <th>Nilai Preferensi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                while ($data = mysqli_fetch_array($result)) :
                ?>

                  <tr class="text-center">
                    <td><?= $no ?></td>
                    <td><?= $data['kode'] ?></td>
                    <td><?= $data['name'] ?></td>
                    <td><?= $data['nilai_preferensi'] ?></td>
                  </tr>

                <?php
                  $no++;
                endwhile;
                ?>
              </tbody>
            </table>
          </div>
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
<script src="../assets/js/page/modules-datatables.js"></script>