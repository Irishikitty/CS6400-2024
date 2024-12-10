<?php
if (isset($_GET['VIN']) || isset($_SESSION['VIN'])) {
    $VehicleVIN = $_GET['VIN'] ?? $_SESSION['VIN'];
    echo "<br>";

    require('update_part_status.php');

    // Access purchase price
    $query = "SELECT Vehicle.purchase_price
    FROM Vehicle WHERE Vehicle.VIN='$VehicleVIN'
    ";
    $result = mysqli_query($db, $query);
    if (mysqli_num_rows($result) > 0) {
        $purchase_price = mysqli_fetch_assoc($result)['purchase_price'];
    } else {
        echo "<p>No vehicles found.</p>";
    }

    // Compute total part cost
    $query = 
    "UPDATE Parts_Order po
    JOIN (
        SELECT p.order_number, SUM(p.unit_price * p.quantity) AS total_cost
        FROM Part p
        GROUP BY p.order_number
    ) AS p_total_cost ON po.order_number = p_total_cost.order_number
    SET po.total_cost = p_total_cost.total_cost
    WHERE po.VIN = '$VehicleVIN';
    ";
    $result = mysqli_query($db, $query);

    $query = "SELECT SUM(Parts_Order.total_cost) As total_cost
    FROM Parts_Order
    WHERE Parts_Order.VIN = '$VehicleVIN'
    ";
    $result = mysqli_query($db, $query);
    if (mysqli_num_rows($result) > 0) {
        $total_part_cost = mysqli_fetch_assoc($result)['total_cost'];
    } else {
        echo "<p>No vehicles found.</p>";
    }

    // Start the HTML table for purchase price and total part cost
    echo "<table border='1' cellpadding='4' cellspacing='0' style='width:80%; text-align:left;'>";
    echo "<tr>";
    echo "<td style='width:50%'><strong>" . htmlspecialchars('Purchase price') . "</strong></td>";
    echo "<td style='width:50%'><strong>" . htmlspecialchars('Total part cost') . "</strong></td>";
    echo "<tr>";
    echo "<td style='width:50%'>$" . number_format((float)$purchase_price, 2, '.', '') . "</td>";
    echo "<td style='width:50%'>$" . number_format((float)$total_part_cost, 2, '.', '')  . "</td>";
    echo "<tr>";
    echo "</table>";
?>
<?php
    $query = " WITH FilterVehicles AS (
        SELECT 
        Vehicle.VIN, Vehicle.vehicle_type, Vehicle.manufacturer_name, Vehicle.model_name,
        Vehicle.model_year, Vehicle.fuel_type, Vehicle.horsepower, Vehicle.sale_price,
        GROUP_CONCAT(Vehicle_color.color ORDER BY Vehicle_color.color SEPARATOR ',') AS vehicle_colors
        FROM Vehicle INNER JOIN Vehicle_color on Vehicle.VIN = Vehicle_color.VIN
        WHERE (UPPER(Vehicle.VIN)=UPPER('$VehicleVIN') or '$VehicleVIN'='')
        GROUP BY Vehicle.VIN
        ORDER BY Vehicle.VIN ASC
        ) 
        SELECT * -- need grouping
        FROM FilterVehicles
        -- LEFT JOIN Part ON FilterVehicles.VIN = Part.VIN
        WHERE NOT EXISTS (
        SELECT 1
        FROM Part P
        WHERE P.VIN = FilterVehicles.VIN
        AND P.status IN ('ordered', 'received')
        )
        -- LEFT JOIN Buy ON FilterVehicles.VIN = Buy.VIN
        AND NOT EXISTS ( 
        SELECT 1
        FROM Buy
        WHERE Buy.VIN = FilterVehicles.VIN
        );";
    $unsold_results = mysqli_query($db, $query);
    $count = mysqli_num_rows($unsold_results);
?>

<?php echo "<h2>Parts and Parts order</h2>"; ?>

<!-- Provide buttons -->
<form action="view_select_vehicle.php" method="POST">
    <!-- Button 1 to go to test.php -->
    <?php if ($count>0 && ($userPermission == '2' || $userPermission == '4')): ?>
    <!-- <button type="submit" name="redirect" value="test" class="button" formaction="add_parts_order.php?VIN=<?php echo htmlspecialchars($VehicleVIN); ?>">Add parts order</button> -->
    <?php endif; ?>
</form>

<?php
    // Access parts order and parts
    $query = 
    "SELECT Part.order_number, Parts_Order.vendor_name,
            Part.vendor_part_number, Part.unit_price,
            Part.quantity, Part.description, Part.status
    FROM Part
    JOIN Parts_Order ON Part.order_number = Parts_Order.order_number
    WHERE Part.VIN = '$VehicleVIN'
    ORDER BY Part.order_number ASC
    ";
    $result = mysqli_query($db, $query);

    
    if (mysqli_num_rows($result) > 0) {
        // Loop through and display each row
        // Start the HTML table
        echo "<table border='1' cellpadding='5' cellspacing='0' style='width:80%';>";
        
        // Print table header (column names)
        echo "<tr>";
        $firstRow = mysqli_fetch_assoc($result);
        foreach ($firstRow as $attribute => $value) {
            echo "<th>" . htmlspecialchars($attribute) . "</th>";
        }
        echo "<th>" . htmlspecialchars('update') . "</th>";
        echo "</tr>";

        echo "<tr>";
        // Rewind the result pointer to the first row after fetching it for the header
        mysqli_data_seek($result, 0);
        // Loop through each row and print the values in table data cells
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($row as $attribute => $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "<td>
                    <form method='POST' action=''>
                        <button type='submit' name='order_number' value='" . htmlspecialchars($row['order_number']) . "' class='button'>" . htmlspecialchars('Update') . "
                        </button>
                        <input type='hidden' name='ven_p_n' value='" . htmlspecialchars($row['vendor_part_number']) . "'>
                        <input type='hidden' name='VIN' value='" . htmlspecialchars($VehicleVIN) . "'>
                        <input type='hidden' name='status' value='" . htmlspecialchars($row['status']) . "'>
                    </form>
                </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No Parts and Parts Order found.</p>";
    }
} else {
    echo "<p>Error: Missing VIN parameter.</p>";
}


?>