<?php
require_once '../layout/_top.php';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>List Kriteria</h1>
      <a href="./create.php" class="btn btn-primary">Tambah Data</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="table-1">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Kriteria</th>
                  <th>Kategori</th>
                    <th style="width: 150">Aksi</th>
                </tr>
              </thead>
              <tbody>
                  <tr>
                    <td>1</td>
                    <td>Promo</td>
                    <td class="text-uppercase">Benefit</td>
                      <td>
                        <a class="btn btn-sm btn-danger mb-md-0 mb-1" href="delete.php">
                          <i class="fas fa-trash fa-fw"></i>
                        </a>
                        <a class="btn btn-sm btn-info" href="edit.php">
                          <i class="fas fa-edit fa-fw"></i>
                        </a>
                      </td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>Metode Pembayaran</td>
                    <td class="text-uppercase">Benefit</td>
                      <td>
                        <a class="btn btn-sm btn-danger mb-md-0 mb-1" href="delete.php">
                          <i class="fas fa-trash fa-fw"></i>
                        </a>
                        <a class="btn btn-sm btn-info" href="edit.php">
                          <i class="fas fa-edit fa-fw"></i>
                        </a>
                      </td>
                  </tr>

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

<script src="../assets/js/page/modules-datatables.js"></script>