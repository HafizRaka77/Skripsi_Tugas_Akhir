<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

global $connection;
$user_id = $_SESSION['login']["id"];

// Fetch criteria, alternatives, and their values
$criteria_query = mysqli_query($connection, 'SELECT * FROM criterias');
$alternatif_query = mysqli_query($connection, "SELECT * FROM alternatives WHERE user_id = '$user_id'");
$alternative_values_query = mysqli_query($connection, "SELECT * FROM alternative_values WHERE user_id = '$user_id'");

// Fetch data into arrays
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
    $normalizedMatrix[$alternativeId][$criteriaId] = $weightedValue; // Store the actual number, not formatted
}

// Compute Positive and Negative Ideal Solutions
$positiveIdealSolution = computeIdealSolution($normalizedMatrix, $criterias, true);
$negativeIdealSolution = computeIdealSolution($normalizedMatrix, $criterias, false);

// Function to compute ideal solutions
function computeIdealSolution(array $weightedMatrix, array $criterias, $isPositive = true)
{
    $idealSolution = [];
    foreach ($criterias as $kriteria) {
        $criteriaId = $kriteria['id'];
        $criteriaType = $kriteria['categories']; // 'cost' or 'benefit'
        $values = array_column($weightedMatrix, $criteriaId);

        // Remove null values
        $values = array_filter($values, function($value) {
            return $value !== null;
        });

        if (empty($values)) {
            $idealSolution[$criteriaId] = null; // Use null if there are no values
        } else {
            if ($isPositive) {
                $idealSolution[$criteriaId] = ($criteriaType === 'cost') ? min($values) : max($values);
            } else {
                $idealSolution[$criteriaId] = ($criteriaType === 'cost') ? max($values) : min($values);
            }
        }
    }
    return $idealSolution;
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
                                        <th><?= htmlspecialchars($kriteria['kode'], ENT_QUOTES, 'UTF-8') ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <th>Solusi ideal +</th>
                                    <?php foreach ($criterias as $kriteria) : ?>
                                        <td>
                                            <?php 
                                                $criteriaId = $kriteria['id'];
                                                echo isset($positiveIdealSolution[$criteriaId]) 
                                                    ? number_format($positiveIdealSolution[$criteriaId], 3) 
                                                    : '-';
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="text-center">
                                    <th>Solusi ideal -</th>
                                    <?php foreach ($criterias as $kriteria) : ?>
                                        <td>
                                            <?php 
                                                $criteriaId = $kriteria['id'];
                                                echo isset($negativeIdealSolution[$criteriaId]) 
                                                    ? number_format($negativeIdealSolution[$criteriaId], 3) 
                                                    : '-';
                                            ?>
                                        </td>
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
