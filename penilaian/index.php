<?php
ob_start(); // Turn on output buffering
require_once '../layout/_top.php';
require_once '../helper/connection.php';

global $connection;
$user_id = $_SESSION['login']["id"];

// Fetch data into an array
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
    $normalizedValue = $divider[$criteriaId] != 0 ? $value['value'] / $divider[$criteriaId] : 0;
    
    // Get the corresponding weight
    $criteria_weight = $criteria_value[$criteriaId] ?? 0; // Default to 0 if weight not found
    
    // Normalize the weight
    $normalized_weight = $weight_sum != 0 ? $criteria_weight / $weight_sum : 0;
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

        if ($isPositive) {
            if ($criteriaType === 'cost') {
                $idealSolution[$criteriaId] = !empty($values) ? min($values) : null;
            } else { // 'benefit'
                $idealSolution[$criteriaId] = !empty($values) ? max($values) : null;
            }
        } else {
            if ($criteriaType === 'cost') {
                $idealSolution[$criteriaId] = !empty($values) ? max($values) : null;
            } else { // 'benefit'
                $idealSolution[$criteriaId] = !empty($values) ? min($values) : null;
            }
        }
    }
    return $idealSolution;
}

// Compute distances from ideal solutions
$positiveDistances = computeDistances($normalizedMatrix, $positiveIdealSolution);
$negativeDistances = computeDistances($normalizedMatrix, $negativeIdealSolution);

// Function to compute distances from ideal solutions
function computeDistances(array $weightedMatrix, array $idealSolution)
{
    $distances = [];
    foreach ($weightedMatrix as $alternativeId => $criteria) {
        $sumOfSquare = 0.0; // Initialize with double type for better precision
        foreach ($criteria as $criteriaId => $value) {
            if (isset($idealSolution[$criteriaId])) {
                $sumOfSquare += pow($value - $idealSolution[$criteriaId], 2);
            }
        }
        $distances[$alternativeId] = sqrt($sumOfSquare);
    }
    return $distances;
}

// Compute Closeness Coefficient
$closenessCoefficient = [];
foreach ($positiveDistances as $alternativeId => $positiveDistance) {
    $negativeDistance = $negativeDistances[$alternativeId];
    $closenessCoefficient[$alternativeId] = ($positiveDistance + $negativeDistance != 0) ? $negativeDistance / ($positiveDistance + $negativeDistance) : 0;
}

// Update preference values in the database
foreach ($closenessCoefficient as $alternativeId => $value) {
    $sql = "UPDATE alternatives SET nilai_preferensi = $value WHERE id = $alternativeId";
    mysqli_query($connection, $sql);
}


$sql = "SELECT * FROM alternatives WHERE user_id = '$user_id' ORDER BY nilai_preferensi DESC";
$result = mysqli_query($connection, $sql);

ob_end_flush(); // Flush buffered content
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Ranking</h1>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="table-1">
              <thead>
                <tr class="text-center">
                  <th>Ranking</th>
                  <th>Kode</th>
                  <th>Nama Alternatif</th>
                  <th>Nilai Preferensi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                while ($data = mysqli_fetch_array($result)) :
                ?>

                  <tr class="text-center">
                    <td><?= $no ?></td>
                    <td><?= $data['kode'] ?></td>
                    <td><?= $data['name'] ?></td>
                    <td><?= number_format($data['nilai_preferensi'],3) ?></td>
                  </tr>

                <?php
                  $no++;
                endwhile;
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>
<!-- Page Specific JS File -->
<?php
if (isset($_SESSION['info'])) :
  if ($_SESSION['info']['status'] == 'success') {
?>
    <script>
      iziToast.success({
        title: 'Sukses',
        message: `<?= $_SESSION['info']['message'] ?>`,
        position: 'topCenter',
        timeout: 5000
      });
    </script>
  <?php
  } else {
  ?>
    <script>
      iziToast.error({
        title: 'Gagal',
        message: `<?= $_SESSION['info']['message'] ?>`,
        timeout: 5000,
        position: 'topCenter'
      });
    </script>
<?php
  }

  unset($_SESSION['info']);
  $_SESSION['info'] = null;
endif;
?>
<script src="../assets/js/page/modules-datatables.js"></script>