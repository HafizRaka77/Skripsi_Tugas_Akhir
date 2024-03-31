<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$criteria = mysqli_query($connection, "SELECT * FROM criterias");

$weight_sum = 0; // Initialize sum variable

// Calculate the total weight sum
while ($data = mysqli_fetch_array($criteria)) {
    $weight_sum += $data['weight'];
}

// Reset criteria query pointer
mysqli_data_seek($criteria, 0);
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Normalisasi Bobot Kriteria</h1>
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
                                    <th>Kriteria</th>
                                    <th>Bobot</th>
                                    <th>Kategori</th>
                                    <th>Normalisasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $normalized_sum = 0; // Initialize normalized weight sum
                                while ($data = mysqli_fetch_array($criteria)) :
                                    // Calculate normalized weight
                                    $normalized_weight = $data['weight'] / $weight_sum;
                                    $normalized_sum += $normalized_weight; // Sum up the normalized weights
                                ?>
                                    <tr class="text-center">
                                        <td><?= $no ?></td>
                                        <td><?= $data['kode'] ?></td>
                                        <td><?= $data['name'] ?></td>
                                        <td><?= $data['weight'] ?></td>
                                        <td class="text-uppercase"><?= $data['categories'] ?></td>
                                        <td><?= number_format($normalized_weight, 3) ?></td>
                                    </tr>
                                <?php
                                    $no++;
                                endwhile;
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="text-center">
                                    <td></td>
                                    <td></td>
                                    <td><b>Jumlah</b></td>
                                    <td><b><?= $weight_sum ?></b></td>
                                    <td></td>
                                    <td><b><?= $normalized_sum ?></b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>
