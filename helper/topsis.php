<?php
include_once 'connection.php';

function getTopsisResult()
{
  global $connection;
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
  } catch (mysqli_sql_exception $e) {
    // Handle SQL exceptions gracefully
    echo "An error occurred while processing the data: " . $e->getMessage();
    die(); // Terminate script execution
  } catch (Exception $e) {
    // Handle other general exceptions
    echo "An unexpected error occurred: " . $e->getMessage();
    die(); // Terminate script execution
  }
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

function closenessCoefficient(array $positiveDistances, array $negativeDistances)
{
  try {
    $closenessCoefficient = [];

    // Check if both arrays are valid
    if (!is_array($positiveDistances) || !is_array($negativeDistances)) {
      throw new InvalidArgumentException('Invalid arguments: Both positiveDistances and negativeDistances must be arrays.');
    }

    // Check if any negative distances are zero
    foreach ($negativeDistances as $value) {
      if ($value === 0) {
        throw new DivisionByZeroError('Division by zero occurred.');
      }
    }

    foreach ($positiveDistances as $alternativeId => $value) {
      $closenessCoefficient[$alternativeId] = number_format($negativeDistances[$alternativeId] / ($negativeDistances[$alternativeId] + $positiveDistances[$alternativeId]), 3);
    }

    return $closenessCoefficient;
  } catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
    die(); // Terminate the script
  }
}

function sortData(array $closenessCoefficient)
{
  arsort($closenessCoefficient);

  $sortedArray = array_combine(array_keys($closenessCoefficient), array_values($closenessCoefficient));

  return $sortedArray;
}