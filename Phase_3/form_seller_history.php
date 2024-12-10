<?php
include('lib/common.php');
?>

<?php

$query = 
"SELECT
    CONCAT(Individual.first_name, ' ', Individual.last_name) AS Individual_name,
    Business.business_name AS Business_name,
    COUNT(Vehicle.VIN) AS num_vehicle,
    AVG(Vehicle.purchase_price) AS avg_purchase,
    SUM(Part.quantity) AS total_part_quantity,
    AVG(Part.quantity*Part.unit_price) AS total_part_cost
FROM Customer
JOIN Vehicle ON Customer.customer_ID = Vehicle.seller_customer_ID
JOIN Part ON Part.VIN = Vehicle.VIN
LEFT JOIN Individual ON Individual.customer_ID = Customer.customer_ID
LEFT JOIN Business ON Business.customer_ID = Customer.customer_ID
GROUP BY Customer.customer_ID, Business.business_name, Individual_name
ORDER BY num_vehicle DESC, avg_purchase ASC
";

$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}

// Fetch and display results
echo "<h2>Seller history</h2>";
if (mysqli_num_rows($result) > 0) {
    // Loop through and display each row
    // Start the HTML table
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    
    // Print table header (column names)
    echo "<tr>";
    echo "<th>Individual Name</th>";
    echo "<th>Business Name</th>";
    echo "<th>Number of Vehicle</th>";
    echo "<th>Average purchase price</th>";
    echo "<th>Total part quantity</th>";
    echo "<th>Total part cost</th>";
    echo "</tr>";
    
    // Print the remaining rows
    echo "<tr>";
    // Rewind the result pointer to the first row after fetching it for the header
    mysqli_data_seek($result, 0);

    // Loop through each row and print the values in table data cells
    while ($row = mysqli_fetch_assoc($result)) {
        // Apply a red background for the first row
        if ($row['total_part_quantity']/$row['num_vehicle'] > 4 || $row['total_part_cost']/$row['num_vehicle'] >= 500){
            $rowStyle = 'style="background-color: red;"';
        } else {
            $rowStyle = '';
        }
        // $rowStyle = ($rowCount === 1) ? 'style="background-color: red;"' : '';
        
        echo "<tr $rowStyle>";
        foreach ($row as $attribute => $value) {
            if (empty($value)) {
                $displayValue = '';
            } elseif (($attribute==='avg_purchase' || $attribute==='total_part_cost') && is_numeric($value)) {
                // Format numeric value to two decimal places and add a dollar sign
                $displayValue = '$' . number_format((float)$value, 2, '.', '');
            }
            else {
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