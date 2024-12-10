<?php
include('lib/common.php');
?>

<?php

$query = 
"SELECT Vehicle_Type.vehicle_type,
        AVG(DATEDIFF(Buy.sale_date, Vehicle.purchase_date)) AS Average_difference
From Vehicle_Type 
Join Vehicle On Vehicle.vehicle_type=Vehicle_Type.vehicle_type
Join Buy On Buy.VIN=Vehicle.VIN
Where Vehicle.purchase_date Is Not Null
And Buy.sale_date Is Not Null
GROUP By Vehicle_Type.vehicle_type";

$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}

// Fetch and display results
echo "<h2>Average Time in Inventory</h2>";
if (mysqli_num_rows($result) > 0) {
    // Loop through and display each row
    // Start the HTML table
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    
    // Print table header
    echo "<tr>";
    echo "<th>Vehicle Type</th>";
    echo "<th>Days in inventory</th>";
    echo "</tr>";
    
    // Print the remaining rows
    echo "<tr>";
    // Rewind the result pointer to the first row after fetching it for the header
    mysqli_data_seek($result, 0);

    // Loop through each row and print the values in table data cells
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        // Vehicle Type Column
        echo "<td>" . (!empty($row['vehicle_type']) ? htmlspecialchars($row['vehicle_type']) : "N/A") . "</td>";
        // Average Difference Column (integer, or N/A if null)
        $averageDifference = !empty($row['Average_difference']) ? intval($row['Average_difference']) : "N/A";
        echo "<td>" . htmlspecialchars($averageDifference) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No vehicles found for the brand: " . htmlspecialchars($user_input_brand) . "</p>";
}

?>