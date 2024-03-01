<?php
require_once '../layout/_top.php';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Bobot Kriteria</h1>
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
                  <th>Kriteria</th>
                  <th>Bobot</th>
                  <th>Kategori</th>
                  <th style="width: 150">Aksi</th>
                </tr>
              </thead>
              <tbody>

                  <tr>
                    <td>1</td>
                    <td>Promo</td>
                    <td></td>
                    <td class="text-uppercase">Benefit</td>
                    <td>
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