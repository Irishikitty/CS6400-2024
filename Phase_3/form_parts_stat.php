<?php
include('lib/common.php');
?>

<?php

$query = 
"SELECT Parts_Order.vendor_name,
        SUM(Part.quantity) AS total_quantity,
        SUM(Part.quantity*Part.unit_price) AS total_dollar_amount
FROM Parts_Order 
INNER JOIN Part ON Parts_Order.VIN = Part.VIN AND Parts_Order.order_number = Part.order_number
GROUP BY Parts_Order.vendor_name
ORDER BY total_dollar_amount DESC";

$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}

// Fetch and display results
echo "<h2>Parts Statistics</h2>";
if (mysqli_num_rows($result) > 0) {
    // Loop through and display each row
    // Start the HTML table
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    
    // Print table header (column names)
    echo "<tr>";
    echo "<th>Vender Name</th>";
    echo "<th>Total part quantity</th>";
    echo "<th>Total spent on parts </th>";
    echo "</tr>";
    
    // Print the remaining rows
    echo "<tr>";
    // Rewind the result pointer to the first row after fetching it for the header
    mysqli_data_seek($result, 0);

    // Loop through each row and print the values in table data cells
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $attribute => $value) {
            if (empty($value)) {
                $displayValue = '';
            } elseif (($attribute==='total_dollar_amount') && is_numeric($value)) {
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