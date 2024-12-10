<!-- <?php //include("lib/header.php"); ?> -->

<?php
include('lib/common.php');
// written by GTusername3

// Check permissions (uncomment if needed)
if ($_SESSION['user_permission_index'] != 1 && $_SESSION['user_permission_index'] != 4) {
    echo "Permission denied";
    exit();
}

if (isset($_GET['VIN'])) {
    $VehicleVIN = $_GET['VIN'];
    $_SESSION['VIN'] = $VehicleVIN;
} elseif (isset($_SESSION['VIN'])) {
    $VehicleVIN = $_SESSION['VIN'];
} else {
    echo $_SESSION['VIN'];
    echo $_GET['VIN'];
    echo "<p>In add parts order.php - Error: Missing VIN parameter.</p>";
    exit();
}

$VendorID = $_SESSION['VendorID'] ?? null;
$_SESSION['VIN'] = $VehicleVIN;

if (empty($VehicleVIN)) {
    echo "<p>Error: Missing or invalid VIN parameter.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_parts_order'])) {

        if (empty($VendorID)) {
            $error_msg = "Error: Missing or invalid Vendor ID. Please select a valid vendor.";
        } elseif (empty($VehicleVIN)) {
            $error_msg = "Error: Missing or invalid VIN parameter. Please go back and try again.";
        } else {
            $query = "SELECT COUNT(*) AS count FROM Parts_Order WHERE VIN = '$VehicleVIN'";
            $result = mysqli_query($db, $query);

            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $num = (int)$row['count'];

                $formattedNum = str_pad($num+1, 3, '0', STR_PAD_LEFT);
                $order_number = $VehicleVIN . '-' . $formattedNum;
                
                $queryInsert = "INSERT INTO Parts_Order (VIN, num, total_cost, vendor_name)
                            VALUES ('$VehicleVIN', $num+1, 0, '$VendorID')";
                $queryID = mysqli_query($db, $queryInsert);


                if ($queryID) {
                    echo "<div style='color: green; font-weight: bold;'>Parts order added successfully with Order Number: $order_number</div>";
                    $_SESSION['current_order_number'] = $order_number;
                } else {
                    $error_msg = "Failed to add parts order: " . htmlspecialchars(mysqli_error($db));
                }
            } else {
                $error_msg = "Error: VIN is not valid or no parts orders found for this VIN.";
            }
        }

    } elseif (isset($_POST['finish_parts_order'])) {
        $current_order_number = $_SESSION['current_order_number'] ?? null;
        if ($current_order_number) {
            $queryUpdate = "UPDATE Parts_Order
                            SET total_cost = (
                                SELECT SUM(unit_price * quantity)
                                FROM Part
                                WHERE Part.order_number = Parts_Order.order_number
                            )
                            WHERE order_number = '$current_order_number'";
            $updateResult = mysqli_query($db, $queryUpdate);

            if ($updateResult) {
                echo "<div style='color: green; font-weight: bold;'>Parts order finalized successfully!</div>";
                unset($_SESSION['current_order_number']);
            } else {
                $error_msg = "Failed to finalize parts order: " . htmlspecialchars(mysqli_error($db));
            }
        } else {
            $error_msg = "No parts order is currently in progress.";
        }
    }
}
?>

<!-- <title>Add Parts Order</title> -->
</head>
<body>
    <div id='main_container'>

        <div class="center_content">
            <div class="center_left">
                <div class="features">
                    <div class="profile_section">
                        <div class="subtitle">Add Parts Order</div>
                        <div>

                            <form action="search_vendor.php" method="GET" style="display: inline;">
                                <button type="submit" class="fancy_button">Search for Vendor</button>
                            </form>
                            <form action="add_vendor.php" method="GET" style="display: inline;">
                                <button type="submit" class="fancy_button">Add a New Vendor</button>
                            </form>
                        </div>
                        <br />

                        <?php if (!empty($VendorID) && !empty($VehicleVIN)): ?>
                        <form name="add_parts_order" action="view_select_vehicle.php" method="POST">
                            <table>
                                <tr>
                                    <td class="item_label">Vendor ID</td>
                                    <td><?php echo $VendorID; ?></td>
                                </tr>
                                <tr>
                                    <td class="item_label">Vehicle VIN</td>
                                    <td><?php echo $VehicleVIN; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <button type="submit" name="add_parts_order" class="fancy_button">Add Parts Order</button>
                                    </td>
                                </tr>
                            </table>
                        </form>

                        <?php if (!empty($_SESSION['current_order_number'])): ?>
                            <div class="subtitle">Added Parts</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Part Number</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $current_order_number = $_SESSION['current_order_number'];
                                    $query = "SELECT vendor_part_number, description, quantity, unit_price, status 
                                            FROM Part WHERE order_number = '$current_order_number'";
                                    $result = mysqli_query($db, $query);

                                    if ($result && mysqli_num_rows($result) > 0):
                                        while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['vendor_part_number']); ?></td>
                                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                                <td><?php echo htmlspecialchars(number_format($row['unit_price'], 2)); ?></td>
                                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                            </tr>
                                        <?php endwhile; 
                                    else: ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; color: red;">No parts added yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>


                        <!-- Add Parts and Finish Parts Order Buttons -->
                        <?php if (!empty($_SESSION['current_order_number'])): ?>
                        <form action="add_parts.php" method="GET" style="display: inline;">
                            <button type="submit" class="fancy_button">Add a New Part</button>
                        </form>
                        <form name="finish_parts_order" action="finish_parts_order.php" method="POST" style="display: inline;">
                            <button type="submit" name="finish_parts_order" class="fancy_button">Finish Parts Order</button>
                        </form>
                        <?php endif; ?>
                        <?php else: ?>
                        <p style="color: red;">No Vendor selected. Please search for or add a Vendor first.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php include("lib/error.php"); ?>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>