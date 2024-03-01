<?php
require_once '../layout/_top.php';
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
                <tr>
                  <th>Ranking</th>
                  <th>Nama Alternatif</th>
                  <th>Nilai Preferensi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td>1</td>
                    <td>Tokopedia</td>
                    <td>0,818</td>
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