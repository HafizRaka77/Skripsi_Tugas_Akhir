<?php
require_once '../layout/_top.php';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Tambah Nilai</h1>
  </div>
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
                      <option value="">Tokopedia</option>
                    
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="criterias" class="form-label">Kriteria:</label>
                </td>
                <td>
                    <div class="row">
                      <div class="col-sm-6 mb-2">
                        <input type="hidden" class="form-control" name="" value="">
                        <input type="text" class="form-control" value="Promo" disabled>
                      </div>
                      <div class="col-sm-6 mb-2">
                        <input type="number" class="form-control" name="weights" required>
                      </div>
                    </div>
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
