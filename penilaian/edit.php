<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$id = $_GET['id']; // Mendapatkan ID alternatif dari parameter URL
$user_id = $_SESSION['login']["id"];


// Ambil data alternatif yang akan diedit dari database
$query_alternative = "SELECT * FROM alternatives WHERE id = $id AND user_id = '$user_id'";
$result_alternative = mysqli_query($connection, $query_alternative);
$data_alternative = mysqli_fetch_assoc($result_alternative);

// Ambil data nilai alternatif yang akan diedit dari database
$query_values = "SELECT * FROM alternative_values WHERE alternative_id = $id AND user_id = '$user_id'";
$result_values = mysqli_query($connection, $query_values);
$values = array();
while ($row = mysqli_fetch_assoc($result_values)) {
    $values[$row['criteria_id']] = $row['value'];
}

// Ambil daftar kriteria
$criteria_query = mysqli_query($connection, "SELECT * FROM criterias");
$criterias = mysqli_fetch_all($criteria_query, MYSQLI_ASSOC);
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Ubah Data Penilaian</h1>
        <a href="data.php" class="btn btn-light">Kembali</a>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Form edit alternatif -->
                    <form action="update.php" method="POST">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode</label>
                            <input type="text" class="form-control" id="kode" name="kode" value="<?= $data_alternative['kode'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= $data_alternative['name'] ?>" required>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kriteria</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($criterias as $kriteria) : ?>
                                        <tr>
                                            <td><?= $kriteria['name'] ?></td>
                                            <td>
                                                <input type="number" class="form-control" name="nilai[<?= $kriteria['id'] ?>]" value="<?= isset($values[$kriteria['id']]) ? $values[$kriteria['id']] : '' ?>" required>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>