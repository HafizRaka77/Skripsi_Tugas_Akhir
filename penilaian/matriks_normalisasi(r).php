<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';


$user_id = $_SESSION['login']['id'] ?? null;

if ($user_id === null) {
    die("User ID tidak ditemukan dalam sesi. Pastikan pengguna sudah login.");
}

// Fetch criterias, alternatives, and alternative values for the logged-in user
$criteria_query = mysqli_query($connection, 'SELECT * FROM criterias');
$alternatif_query = mysqli_query($connection, "SELECT * FROM alternatives WHERE user_id = $user_id");
$alternative_values_query = mysqli_query($connection, "SELECT * FROM alternative_values WHERE user_id = $user_id");
$criteria_value_query = mysqli_query($connection, "SELECT * FROM criteria_values WHERE user_id = $user_id");

$criterias = mysqli_fetch_all($criteria_query, MYSQLI_ASSOC);
$alternatives = mysqli_fetch_all($alternatif_query, MYSQLI_ASSOC);
$alternative_values = mysqli_fetch_all($alternative_values_query, MYSQLI_ASSOC);
$criteria_values = array_column(mysqli_fetch_all($criteria_value_query, MYSQLI_ASSOC), null, 'criteria_id');

// Initialize arrays for sum of squares and normalized values
$sumOfSquares = [];
$divider = [];
$normalizedValues = [];

// Calculate sum of squares for each criterion
foreach ($criterias as $kriteria) {
    $criteria_id = $kriteria['id'];
    $sumOfSquares[$criteria_id] = 0.0;
    foreach ($alternative_values as $value) {
        if ($value['criteria_id'] == $criteria_id) {
            $sumOfSquares[$criteria_id] += pow($value['value'], 2);
        }
    }
    $divider[$criteria_id] = sqrt($sumOfSquares[$criteria_id]);
}

// Calculate normalized values for each alternative
foreach ($alternative_values as $value) {
    $criteria_id = $value['criteria_id'];
    $alternative_id = $value['alternative_id'];
    $normalizedValues[$alternative_id][$criteria_id] = $divider[$criteria_id] != 0 ? $value['value'] / $divider[$criteria_id] : 0;
}

?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Matriks Normalisasi (r)</h1>
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
                                <?php foreach ($alternatives as $alternatif) : ?>
                                    <tr class="text-center">
                                        <td><?= htmlspecialchars($alternatif['kode'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <?php foreach ($criterias as $kriteria) : ?>
                                            <td>
                                                <?php
                                                $normalized_value = $normalizedValues[$alternatif['id']][$kriteria['id']] ?? '-';
                                                echo '<h6>' . (is_numeric($normalized_value) ? number_format($normalized_value, 3) : $normalized_value) . '</h6>';
                                                ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="text-center">
                                    <th>Bobot</th>
                                    <?php 
                                    $weight_sum = array_sum(array_column($criteria_values, 'weight'));
                                    foreach ($criterias as $kriteria) : 
                                        $weight = $criteria_values[$kriteria['id']]['weight'] ?? 0;
                                        if ($weight_sum > 0) {
                                            $normalizedWeight = $weight / $weight_sum;
                                            echo '<th>' . number_format($normalizedWeight, 4) . '</th>';
                                        } else {
                                            echo '<th>-</th>';
                                        }
                                    endforeach; ?>
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
