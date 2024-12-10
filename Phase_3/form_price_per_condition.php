<?php
include('lib/common.php');
?>

<?php

$query = "SELECT Vehicle_Type.vehicle_type,
AVG(CASE WHEN Vehicle.vehicle_condition = 'Excellent' THEN Vehicle.purchase_price END) AS Excellent,
AVG(CASE WHEN Vehicle.vehicle_condition = 'Very Good' THEN Vehicle.purchase_price END) AS Very_Good,
AVG(CASE WHEN Vehicle.vehicle_condition = 'Good' THEN Vehicle.purchase_price END) AS Good,
AVG(CASE WHEN Vehicle.vehicle_condition = 'Fair' THEN Vehicle.purchase_price END) AS Fair
FROM Vehicle_Type
LEFT JOIN Vehicle ON Vehicle_Type.vehicle_type = Vehicle.vehicle_type
GROUP BY Vehicle_Type.vehicle_type;";

$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}

// Fetch and display results
echo "<h2>Price Per Condition</h2>";
if (mysqli_num_rows($result) > 0) {
    // Loop through and display each row
    // Start the HTML table
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    
    // Print table header (column names)
    echo "<tr>";
    $firstRow = mysqli_fetch_assoc($result);
    foreach ($firstRow as $attribute => $value) {
        echo "<th>" . htmlspecialchars($attribute) . "</th>";
    }
    echo "</tr>";
    
    // Print the remaining rows
    echo "<tr>";
    // Rewind the result pointer to the first row after fetching it for the header
    mysqli_data_seek($result, 0);

    // Loop through each row and print the values in table data cells
    // Loop through each row and print the values in table data cells
    while ($row = mysqli_fetch_assoc($result)) {
        // Check if all numeric columns are empty/null for this row
        $allEmpty = true;
        foreach ($row as $attribute => $value) {
            if ($attribute !== 'vehicle_type' && !empty($value) && $value !== NULL) {
                $allEmpty = false;
                break;
            }
        }
        
        // Skip this row if all numeric columns are empty/null
        if ($allEmpty) {
            continue;
        }
        
        // Print the row
        echo "<tr>";
        foreach ($row as $attribute => $value) {
            // Replace empty/null values with 'N/A'
            if (empty($value) && $value !== '0') {
                $displayValue = '$0.00';
            } elseif (is_numeric($value)) {
                // Format numeric value to two decimal places and add a dollar sign
                $displayValue = '$' . number_format((float)$value, 2, '.', '');
            } else {
                $displayValue = htmlspecialchars($value);
            }
            echo "<td>" . $displayValue . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No vehicles found for the brand: " . htmlspecialchars($user_input_brand) . "</p>";
}

?>