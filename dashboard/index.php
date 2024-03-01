<?php
require_once '../layout/_top.php';
?>

<section class="section">
  <div class="section-header">
    <h1>Dashboard</h1>
  </div>
  <div>
    <center><h3>Selamat Datang di Sistem Informasi Penilaian E-commerce</h3></center>
  </div><br>
  <div class="column">
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="far fa-solid fa-shop"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Alternatif</h4>
            </div>
            <div class="card-body">
              <p>5</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="far fa-user"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Kriteria</h4>
            </div>
            <div class="card-body">
              <p>7</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>