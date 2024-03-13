<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$result = mysqli_query($connection, "SELECT * FROM criterias");
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>List Kriteria</h1>
    <?php if ($role == 1) : ?>
      <a href="./create.php" class="btn btn-primary">Tambah Data</a>
    <?php endif; ?>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="table-1">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Kode</th>
                  <th>Nama Kriteria</th>
                  <th>Kategori</th>
                  <?php if ($role == 1) : ?>
                    <th style="width: 150">Aksi</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                while ($data = mysqli_fetch_array($result)) :
                ?>

                  <tr class="text-center">
                    <td><?= $no++ ?></td>
                    <td><?= $data['kode'] ?></td>
                    <td><?= $data['name'] ?></td>
                    <td class="text-uppercase"><?= $data['categories'] ?></td>
                    <?php if ($role == 1) : ?>
                      <td>
                        <a class="btn btn-sm btn-danger mb-md-0 mb-1" href="delete.php?id=<?= $data['id'] ?>" role="button" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')">
                          <i class="fas fa-trash fa-fw"></i>
                        </a>
                        <a class="btn btn-sm btn-info" href="edit.php?id=<?= $data['id'] ?>">
                          <i class="fas fa-edit fa-fw"></i>
                        </a>
                      </td>
                    <?php endif; ?>
                  </tr>

                <?php
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