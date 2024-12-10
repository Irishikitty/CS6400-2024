<?php
include('lib/common.php');
?>

<?php

$query = 
"SELECT YEAR(Buy.sale_date) AS sale_year,
        MONTH(Buy.sale_date) AS sale_month,
        COUNT(Buy.VIN) AS total_vehicles_sold,
        SUM(Vehicle.sale_price) AS gross_sales_income,
        SUM(Vehicle.sale_price - Vehicle.purchase_price - COALESCE(p.total_expenses,0)) AS total_net_income
FROM Buy
INNER JOIN Vehicle ON Buy.VIN = Vehicle.VIN
LEFT JOIN (SELECT VIN, SUM(total_cost) AS total_expenses
            FROM Parts_Order
            GROUP BY VIN) p ON Vehicle.VIN = p.VIN
GROUP BY sale_year, sale_month
ORDER BY sale_year DESC, sale_month DESC
";

$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}

// Fetch and display results
echo "<h2>Monthly Sales</h2>";
if (mysqli_num_rows($result) > 0) {
    // Loop through and display each row
    // Start the HTML table
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    
    // Print table header (column names)
    echo "<tr>";
    echo "<th>Year</th>";
    echo "<th>Month</th>";
    echo "<th>Total vehicle sold</th>";
    echo "<th>Gross income</th>";
    echo "<th>Net income</th>";
    echo "</tr>";
    
    // Rewind the result pointer to the first row after fetching it for the header
    mysqli_data_seek($result, 0);

    // Print the remaining rows
    echo "<tr>";
    // Rewind the result pointer to the first row after fetching it for the header
    mysqli_data_seek($result, 0);

    // Loop through each row and print the values in table data cells
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $attribute => $value) {
            // If the column is 'sale_month', make it a clickable link
            if ($attribute === 'sale_month') {
                echo "<td><a href='?year=" . htmlspecialchars($row['sale_year']) . "&month=" . htmlspecialchars($value) . "'>" . htmlspecialchars($value) . "</a></td>";
            } elseif (($attribute==='gross_sales_income' || $attribute==='total_net_income') && is_numeric($value)) {
                // Format numeric value to two decimal places and add a dollar sign
                $displayValue = '$' . number_format((float)$value, 2, '.', '');
                echo "<td>" . $displayValue . "</td>";
            } else {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No vehicles found for the brand: " . htmlspecialchars($user_input_brand) . "</p>";
}

// Handle second query if year and month are set
if (isset($_GET['year']) && isset($_GET['month'])) {
    $selectedYear = intval($_GET['year']);
    $selectedMonth = intval($_GET['month']);

    // Example second query for vehicles sold in the selected month
    $secondQuery = 
    "SELECT CONCAT(Logged_in_User.first_name, ' ', Logged_in_User.last_name) As sale_name,
        COUNT(Buy.VIN) AS vehicles_sold,
        SUM(Vehicle.sale_price) AS total_sales
    FROM Buy
    INNER JOIN Vehicle ON Buy.VIN = Vehicle.VIN
    INNER JOIN Logged_in_User ON Buy.salespeople_username = Logged_in_User.username
    WHERE YEAR(Buy.sale_date) = '$selectedYear' AND MONTH(Buy.sale_date) = '$selectedMonth'
    GROUP By Logged_in_User.username
    ORDER BY vehicles_sold DESC, total_sales DESC
    ";

    $secondResult = mysqli_query($db, $secondQuery);

    echo "<h2>Details for $selectedMonth/$selectedYear</h2>";

    if (mysqli_num_rows($secondResult) > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";

        echo "<tr>";
        $firstRow = mysqli_fetch_assoc($secondResult);
        foreach ($firstRow as $attribute => $value) {
            echo "<th>" . htmlspecialchars($attribute) . "</th>";
        }
        echo "</tr>";
        mysqli_data_seek($secondResult, 0);

        while ($row = mysqli_fetch_assoc($secondResult)) {
            echo "<tr>";
            foreach ($row as $attribute => $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No vehicles found for $selectedMonth/$selectedYear.</p>";
    }
}

?>