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
try {
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

    $positiveDistances = positiveDistances($weightedMatrix, $positiveIdealSolution);
    $negativeDistances = negativeDistances($weightedMatrix, $negativeIdealSolution);
    $closenessCoefficient = closenessCoefficient($positiveDistances, $negativeDistances);

    foreach ($closenessCoefficient as $alternativeId => $value) {
        $sql = "UPDATE alternatives SET nilai_preferensi = $value WHERE id = $alternativeId";
        mysqli_query($connection, $sql);
    }
} catch (Exception $e) {
    // Handle exceptions
}

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

function positiveDistances(array $weightedMatrix, array $positiveIdealSolution)
{
    $positiveDistances = [];

    foreach ($weightedMatrix as $alternativeId => $criteria) {
        $sumOfSquare = 0.0; // Initialize with double type for better precision

        foreach ($criteria as $criteriaId => $value) {
            $sumOfSquare += pow($value - $positiveIdealSolution[$criteriaId], 2);
        }

        $sqrtSumOfSquare = sqrt($sumOfSquare);
        $positiveDistances[$alternativeId] = number_format($sqrtSumOfSquare, 3);
    }

    return $positiveDistances;
}

function negativeDistances(array $weightedMatrix, array $negativeIdealSolution)
{
    $negativeDistances = [];

    foreach ($weightedMatrix as $alternativeId => $criteria) {
        $sumOfSquare = 0.0; // Initialize with double type for better precision

        foreach ($criteria as $criteriaId => $value) {
            $sumOfSquare += pow($value - $negativeIdealSolution[$criteriaId], 2);
        }

        $sqrtSumOfSquare = sqrt($sumOfSquare);
        $negativeDistances[$alternativeId] = number_format($sqrtSumOfSquare, 3);
    }

    return $negativeDistances;
}
function closenessCoefficient($positiveDistances, $negativeDistances)
{
    $closenessCoefficient = [];

    foreach ($positiveDistances as $alternativeId => $positiveDistance) {
        if (isset($negativeDistances[$alternativeId]) && $negativeDistances[$alternativeId] != 0) {
            $closenessCoefficient[$alternativeId] = $negativeDistances[$alternativeId] / ($positiveDistance + $negativeDistances[$alternativeId]);
        } else {
            $closenessCoefficient[$alternativeId] = 0;
        }
    }

    return $closenessCoefficient;
}
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1> Jarak Nilai Alternatif Dari Matriks Solusi Ideal Positif & Negatif</h1>
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
                                    <th>Jarak Solusi +</th>
                                    <th>Jarak Solusi -</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alternatives as $alternatif) : ?>
                                    <tr class="text-center">
                                        <td><?= $alternatif['kode'] ?></td>
                                        <td><?= isset($positiveDistances[$alternatif['id']]) ? $positiveDistances[$alternatif['id']] : '-' ?></td>
                                        <td><?= isset($negativeDistances[$alternatif['id']]) ? $negativeDistances[$alternatif['id']] : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
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
