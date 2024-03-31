<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

global $connection;
$criteria_query = mysqli_query($connection, 'SELECT * FROM criterias');
$alternatif_query = mysqli_query($connection, 'SELECT * FROM alternatives');
$alternative_values_query = mysqli_query($connection, "SELECT * FROM alternative_values");

// Fetch data into an array
$criterias = mysqli_fetch_all($criteria_query, MYSQLI_ASSOC);
$alternatives = mysqli_fetch_all($alternatif_query, MYSQLI_ASSOC);
$alternative_values = mysqli_fetch_all($alternative_values_query, MYSQLI_ASSOC);

    $sql = "SELECT COUNT(*) AS total_criteria FROM criterias";
    $result = mysqli_query($connection, $sql);
    $criteriaLength = mysqli_fetch_assoc($result)['total_criteria'];

    $sqlWeight = "SELECT * FROM criterias";
    $resultWeight = mysqli_query($connection, $sqlWeight);
    $resultWeight = mysqli_fetch_all($resultWeight, MYSQLI_ASSOC);

    $divider = normalizedWeight($criteriaLength);
    $normalizedMatrix = $divider['normalizedValue'];
    $weight = array_column($resultWeight, 'weight', 'id');
    $weight = array_map('floatval', $weight);
    $weightedMatrix = weightedMatrix($normalizedMatrix, $weight);
    $positiveIdealSolution = positiveIdealSolution($weightedMatrix);
    $negativeIdealSolution = negativeIdealSolution($weightedMatrix);

function normalizedWeight($criteriaLength)
{
    global $connection;
    $divider = [];
    $normalizedValue = [];

    for ($criteriaId = 1; $criteriaId <= $criteriaLength; $criteriaId++) {
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

    return [
        'divider' => $divider, // Maintain string format for display
        'normalizedValue' => $normalizedValue, // Now contains numeric values
    ];
}

function weightedMatrix(array $normalizedMatrix, $weight)
{
    $weightedValue = [];

    foreach ($normalizedMatrix as $alternativeId => $criteria) {
        foreach ($criteria as $criteriaId => $value) {
            $weightResult = $value * $weight[$criteriaId];
            $weightedValue[$alternativeId][$criteriaId] = (float) number_format($weightResult, 3);
        }
    }

    return $weightedValue;
}

function positiveIdealSolution(array $weightedMatrix)
{
    global $connection;
    $criteriaBenefit = mysqli_query($connection, "SELECT * FROM criterias WHERE categories = 'benefit'");
    $criteriaBenefit = mysqli_fetch_all($criteriaBenefit, MYSQLI_ASSOC);
    $criteriaBenefit = array_column($criteriaBenefit, 'id');
    $positiveIdealSolution = [];
    foreach ($weightedMatrix as $alternativeId => $criteria) {
        foreach ($criteria as $criteriaId => $value) {
            if (in_array($criteriaId, $criteriaBenefit)) {
                if (!isset($positiveIdealSolution[$criteriaId])) {
                    $positiveIdealSolution[$criteriaId] = (float)$value;
                } else {
                    if ($positiveIdealSolution[$criteriaId] < $value) {
                        $positiveIdealSolution[$criteriaId] = (float)$value;
                    }
                }
            } else {
                if (!isset($positiveIdealSolution[$criteriaId])) {
                    $positiveIdealSolution[$criteriaId] = (float)$value;
                } else {
                    if ($positiveIdealSolution[$criteriaId] > $value) {
                        $positiveIdealSolution[$criteriaId] = (float)$value;
                    }
                }
            }
        }
    }

    return $positiveIdealSolution;
}

function negativeIdealSolution(array $weightedMatrix)
{
    global $connection;
    $criteriaBenefit = mysqli_query($connection, "SELECT * FROM criterias WHERE categories = 'cost'");
    $criteriaBenefit = mysqli_fetch_all($criteriaBenefit, MYSQLI_ASSOC);
    $criteriaBenefit = array_column($criteriaBenefit, 'id');
    $negativeIdealSolution = [];
    foreach ($weightedMatrix as $alternativeId => $criteria) {
        foreach ($criteria as $criteriaId => $value) {
            if (in_array($criteriaId, $criteriaBenefit)) {
                if (!isset($negativeIdealSolution[$criteriaId])) {
                    $negativeIdealSolution[$criteriaId] = (float)$value;
                } else {
                    if ($value > $negativeIdealSolution[$criteriaId]) {
                        $negativeIdealSolution[$criteriaId] = (float)$value;
                    }
                }
            } else {
                if (!isset($negativeIdealSolution[$criteriaId])) {
                    $negativeIdealSolution[$criteriaId] = (float)$value;
                } else {
                    if ($value < $negativeIdealSolution[$criteriaId]) {
                        $negativeIdealSolution[$criteriaId] = (float)$value;
                    }
                }
            }
        }
    }

    return $negativeIdealSolution;
}

?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Matriks Ideal Positif & Negatif</h1>
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
                                <tr class="text-center">
                                    <th>Solusi ideal +</th>
                                    <?php foreach ($positiveIdealSolution as $value) : ?>
                                        <td><?= number_format($value, 3) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="text-center">
                                    <th>Solusi ideal -</th>
                                    <?php foreach ($negativeIdealSolution as $value) : ?>
                                        <td><?= number_format($value, 3) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
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