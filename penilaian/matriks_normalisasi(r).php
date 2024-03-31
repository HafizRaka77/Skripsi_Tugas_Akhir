<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$criteria_query = mysqli_query($connection, 'SELECT * FROM criterias');
$alternatif_query = mysqli_query($connection, 'SELECT * FROM alternatives');
$alternative_values_query = mysqli_query($connection, "SELECT * FROM alternative_values");

// Fetch data into an array
$criterias = mysqli_fetch_all($criteria_query, MYSQLI_ASSOC);
$alternatives = mysqli_fetch_all($alternatif_query, MYSQLI_ASSOC);
$alternative_values = mysqli_fetch_all($alternative_values_query, MYSQLI_ASSOC);
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Matrik Normalisasi (r)</h1>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped w-100" id="table-1">
                            <thead>
                                <tr class="text-center">
                                    <th>Kode</th>
                                    <?php foreach ($criterias as $kriteria) : ?>
                                        <th><?= $kriteria['kode'] ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alternatives as $alternatif) : ?>
                                    <tr class="text-center">
                                        <td><?= $alternatif['kode'] ?></td>
                                        <?php foreach ($criterias as $kriteria) : ?>
                                            <td>
                                                <?php 
                                                $alternatif_value = null;
                                                foreach ($alternative_values as $value) {
                                                    if ($value['alternative_id'] == $alternatif['id'] && $value['criteria_id'] == $kriteria['id']) {
                                                        $alternatif_value = $value['value'];
                                                        break;
                                                    }
                                                }
                                                if ($alternatif_value !== null) {
                                                    $divider = [];
                                                    $normalizedValue = [];

                                                    for ($criteriaId = 1; $criteriaId <= count($criterias); $criteriaId++) {
                                                        $sumOfSquare = 0.0; // Initialize with double type for better precision
                                                        
                                                        $alternativeValues = mysqli_query($connection, "SELECT * FROM alternative_values WHERE criteria_id = $criteriaId");
                                                        while ($alternativeValue = mysqli_fetch_assoc($alternativeValues)) {
                                                            $value = (float) number_format($alternativeValue['value'], 3);
                                                            $sumOfSquare += pow($value, 2);
                                                        }
                                                        
                                                        $sqrtSumOfSquare = sqrt($sumOfSquare);
                                                        $divider[$criteriaId] = number_format($sqrtSumOfSquare, 3);
                                                    }
                                                    
                                                    foreach ($divider as $criteriaId => $value) {
                                                        $alternativeValues = mysqli_query($connection, "SELECT * FROM alternative_values WHERE criteria_id = $criteriaId");
                                                        while ($alternativeValue = mysqli_fetch_assoc($alternativeValues)) {
                                                            $normalizedValue[$alternativeValue['alternative_id']][$criteriaId] = (float) number_format($alternativeValue['value'] / $value, 3);
                                                        }
                                                    }

                                                    // Display the normalized value for the current alternative and criteria
                                                    echo '<h6>' . ($normalizedValue[$alternatif['id']][$kriteria['id']] ?? '-') . '</h6>';
                                                } else {
                                                    echo '<h6>-</h6>';
                                                }
                                                ?>
                                            </td> 
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <thead>
                                <tr class="text-center">
                                    <th>Bobot</th>
                                    <?php foreach ($criterias as $kriteria) : ?>
                                        <th><?= $kriteria['weight'] ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>    
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
