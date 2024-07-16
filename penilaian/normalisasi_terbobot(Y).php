<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$user_id = $_SESSION['login']["id"];

// Fetch criteria, alternatives, and alternative values
$criteria_query = mysqli_query($connection, 'SELECT * FROM criterias');
$alternatif_query = mysqli_query($connection, "SELECT * FROM alternatives WHERE user_id = '$user_id'");
$alternative_values_query = mysqli_query($connection, "SELECT * FROM alternative_values WHERE user_id = '$user_id'");

$criterias = mysqli_fetch_all($criteria_query, MYSQLI_ASSOC);
$alternatives = mysqli_fetch_all($alternatif_query, MYSQLI_ASSOC);
$alternative_values = mysqli_fetch_all($alternative_values_query, MYSQLI_ASSOC);

// Fetch criteria weights
$criteria_value_query = mysqli_query($connection, "SELECT criteria_id, weight FROM criteria_values WHERE user_id = '$user_id'");
$criteria_value = [];
while ($row = mysqli_fetch_assoc($criteria_value_query)) {
    $criteria_value[$row['criteria_id']] = $row['weight'];
}

// Calculate total weight sum
$weight_sum = array_sum($criteria_value);

// Calculate the divider for normalization
$divider = [];
foreach ($criterias as $kriteria) {
    $sumOfSquare = 0.0;
    foreach ($alternative_values as $value) {
        if ($value['criteria_id'] == $kriteria['id']) {
            $sumOfSquare += pow($value['value'], 2);
        }
    }
    $divider[$kriteria['id']] = sqrt($sumOfSquare);
}

// Calculate normalized and weighted values
$normalizedMatrix = [];
foreach ($alternative_values as $value) {
    $criteriaId = $value['criteria_id'];
    $alternativeId = $value['alternative_id'];
    $normalizedValue = $value['value'] / $divider[$criteriaId];
    
    // Get the corresponding weight
    $criteria_weight = $criteria_value[$criteriaId] ?? 0; // Default to 0 if weight not found
    
    // Normalize the weight
    $normalized_weight = $criteria_weight / $weight_sum;
    $weightedValue = $normalizedValue * $normalized_weight;

    // Store in normalized matrix
    $normalizedMatrix[$alternativeId][$criteriaId] = number_format($weightedValue, 3, '.', '');
}

?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Normalisasi Terbobot (Y)</h1>
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
                                                $criteriaId = $kriteria['id'];
                                                $alternativeId = $alternatif['id'];
                                                echo '<h6>' . ($normalizedMatrix[$alternativeId][$criteriaId] ?? '-') . '</h6>';
                                                ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <thead>
                                <tr class="text-center">
                                    <th>Keterangan</th>
                                    <?php foreach ($criterias as $kriteria) : ?>
                                        <th><?= $kriteria['categories'] ?></th>
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
