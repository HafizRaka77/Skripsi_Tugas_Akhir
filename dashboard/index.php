<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$user_id = $_SESSION['login']["id"];
// $id_role = $_SESSION['login']["role_id"];
// $role = query_find("role", "id = '$id_role'");

$alternatives = query_select("alternatives");

$list = [];
foreach ($alternatives as $valll) {
  if (!isset($list[$valll["kode"]])) {
    $list[$valll["kode"]] = true;
  }
}

$total_alternatives = count($list);
$criterias = mysqli_query($connection, "SELECT COUNT(*) FROM criterias");

$total_criterias = mysqli_fetch_array($criterias)[0];
?>

<?php if ($role == 1) : ?>
<section class="section">
  <div class="section-header">
    <h1>Dashboard</h1>
  </div>
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
              <?= $total_alternatives ?>
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
              <?= $total_criterias ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
<?php if ($role !== 1) : ?>
<section class="section">
  <div class="section-header">
    <h1>Dashboard</h1>
  </div>
  <div class="container mt-3">
        <div class="p-2 text-center">
            <div class="p-5 row align-items-center justify-content-center">
                <div class="col-lg-8">
                    <h1 class="pb-3 fs-3">Sistem Penilaian E-Commerce Menggunakan Metode Kombinasi SMART Dan TOPSIS
                    </h1>
                    <p class="fw-normal fs-10 text-muted">
                        Metode SMART (Simple Multi Attribute Rating Tecհnique) dan TOPSIS (Technique for Others Preference by Similarity) 
                        adalah dua metode pengambilan keputusan yang digunakan dalam sistem penilaian e-commerce dengan memberikan hasil akhir 
                        berupa nilai preferensi.
                    </p>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>
<?php endif; ?>
<?php
require_once '../layout/_bottom.php';
?>