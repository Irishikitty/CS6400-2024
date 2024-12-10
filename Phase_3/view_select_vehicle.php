<?php
include('lib/common.php');
?>

<!-- Read the permission index and vehicle VIN -->
<?php
if (isset($_SESSION['user_permission_index'])) {
    $userPermission = $_SESSION['user_permission_index'];
} else {
    $_SESSION['user_permission_index'] = '0';
    $userPermission = $_SESSION['user_permission_index'];
}
echo "<h2>Car Details</h2>";

if (isset($_GET['VIN']) || isset($_SESSION['VIN'])) {
    $VehicleVIN = $_GET['VIN'] ?? $_SESSION['VIN'];
    $_SESSION['VIN'] = $VehicleVIN;
} else {
    echo $_SESSION['VIN'];
    echo $_GET['VIN'];
    echo "<p>In view select vehicle.php Error: Missing VIN parameter.</p>";
    exit();
}

?>

<!-- Determine sold or not for button display -->
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
   

<!-- Provide buttons -->
<form action="view_select_vehicle.php" method="POST">
    <!-- Button 1 to go to test.php -->
    <button type="submit" name="redirect" value="test" class="button" formaction="default_search_page.php">return</button>
    <!-- Button 2: Stay in view.php -->
    <?php if ($count>0 && ($userPermission == '2' || $userPermission == '4')): ?> <!-- salespeople =2 or owner=4-->
        <button type="submit" name="sellcar" value="sell" class="button" formaction="sell_vehicle.php?VIN=<?php echo htmlspecialchars($VehicleVIN); ?>">Sell the vehicle</button>
    <?php endif; ?>
</form>

<!-- Diplay details and execute subtasks when no button is pressed -->
<?php
// Update total parts cost
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

// Update sale price
$query = 
"UPDATE Vehicle
SET Vehicle.sale_price = 1.25*Vehicle.purchase_price + 1.10*(
SELECT IFNULL (SUM(Parts_Order.total_cost), 0)
FROM Parts_Order
WHERE Parts_Order.VIN = '$VehicleVIN' 
)
WHERE Vehicle.VIN = '$VehicleVIN' 
";
mysqli_query($db, $query);

// Display vehicle details
$query = "SELECT 
Vehicle.VIN, Vehicle.vehicle_type, Vehicle.manufacturer_name, Vehicle.model_name,
Vehicle.model_year, Vehicle.fuel_type, Vehicle.horsepower, Vehicle.sale_price,
GROUP_CONCAT(Vehicle_color.color ORDER BY Vehicle_color.color SEPARATOR ',') AS vehicle_colors,
Vehicle.description
FROM Vehicle 
LEFT JOIN Vehicle_color ON Vehicle.VIN = Vehicle_color.VIN
WHERE UPPER(Vehicle.VIN) = UPPER('$VehicleVIN')
GROUP BY Vehicle.VIN";

$result = mysqli_query($db, $query);

if (mysqli_num_rows($result) > 0) {
    // Fetch the first row from the result set
    $firstRow = mysqli_fetch_assoc($result);

    // Start the HTML table
    echo "<table border='1' cellpadding='4' cellspacing='0' style='width:80%; text-align:left;'>";
    echo "<tr>";
    echo "<td colspan='1'><strong>" . htmlspecialchars('VIN') . "</strong></td>";
    echo "<td colspan='1'><strong>" . htmlspecialchars('Vehicel Type') . "</strong></td>";
    echo "<td colspan='1'><strong>" . htmlspecialchars('Sale price') . "</strong></td>";
    echo "</tr>";
    echo "<td colspan='1'>" . htmlspecialchars($firstRow['VIN']) . "</td>";
    echo "<td colspan='1'>" . htmlspecialchars($firstRow['vehicle_type']) . "</td>";
    echo "<td colspan='1'>" . '$' . htmlspecialchars($firstRow['sale_price']) . "</td>";
    echo "<tr>";
    echo "<td colspan='1'><strong>" . htmlspecialchars('Manufacturer') . "</strong></td>";
    echo "<td colspan='1'><strong>" . htmlspecialchars('Model') . "</strong></td>";
    echo "<td colspan='1'><strong>" . htmlspecialchars('Year') . "</strong></td>";
    echo "</tr>";
    echo "<td colspan='1'>" . htmlspecialchars($firstRow['manufacturer_name']) . "</td>";
    echo "<td colspan='1'>" . htmlspecialchars($firstRow['model_name']) . "</td>";
    echo "<td colspan='1'>" . htmlspecialchars($firstRow['model_year']) . "</td>";
    echo "<tr>";
    echo "<td colspan='1'><strong>" . htmlspecialchars('Fuel') . "</strong></td>";
    echo "<td colspan='1'><strong>" . htmlspecialchars('Vehicle color') . "</strong></td>";
    echo "<td colspan='1'><strong>" . htmlspecialchars('Horsepower') . "</strong></td>";
    echo "</tr>";
    echo "<td colspan='1'>" . htmlspecialchars($firstRow['fuel_type']) . "</td>";
    echo "<td colspan='1'>" . htmlspecialchars($firstRow['vehicle_colors']) . "</td>";
    echo "<td colspan='1'>" . htmlspecialchars($firstRow['horsepower']) . "</td>";
    echo "<tr>";
    echo "<td colspan='3'><strong>" . htmlspecialchars('Description') . "</strong></td>";
    echo "</tr>";
    echo "<td colspan='3'>" . htmlspecialchars(($firstRow['description']==Null) ? '' : $firstRow['description']) . "</td>";
    echo "</tr>";
    // End the HTML table
    echo "</table>";
} else {
    echo "<p>No vehicles found.</p>";
}

// if user is clerk or owner, execute view purchase price and parts subtask
if ($userPermission == '1' || $userPermission == '3' || $userPermission == '4'){  // inventory clerk=1 or manager=3,owner=4
    include('view_purprice_parts.php');
    include('add_parts_order.php');
}

// if user is manager or owner, execute view vehicle sell and buy history subtask
if ($userPermission == '3' || $userPermission == '4'){ // manager=3,owner=4
    include('view_sell_buy_hist.php');
}
?>