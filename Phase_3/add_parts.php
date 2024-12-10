<?php
include('lib/common.php');

$order_number = $_SESSION['current_order_number'] ?? null;
$VehicleVIN = $_SESSION['VIN'] ?? null;

if (!$order_number) {
    echo "Error: No active parts order. Please start a new order.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vendor_part_number = mysqli_real_escape_string($db, $_POST['vendor_part_number']);
    $description = mysqli_real_escape_string($db, $_POST['description']);
    $quantity = (int)$_POST['quantity'];
    $unit_price = (float)$_POST['unit_price'];
    $status = mysqli_real_escape_string($db, $_POST['status']);

    $query = "INSERT INTO Part (VIN, order_number, vendor_part_number, status, description, unit_price, quantity)
              VALUES ('$VehicleVIN', '$order_number', '$vendor_part_number', '$status', '$description', $unit_price, $quantity)";

    if (mysqli_query($db, $query)) {
        header("Location: view_select_vehicle.php");
        exit();
    } else {
        echo "Error adding part: " . mysqli_error($db);
    }
}
?>

<?php include("lib/header.php"); ?>
<title>Add Part</title>
</head>
<body>
    <div id="main_container">
        <?php include("lib/menu.php")?>

        <div class="center_content">
            <div class="center_left">
                <div class="features">

                    <div class="profile_section">
                        <div class="subtitle">Add New Part</div>

                        <!-- Add Part Form -->
                        <form name="addPartForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                            <fieldset style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                <legend style="font-weight: bold;">Part Details</legend>

                                <div style="margin-bottom: 15px;">
                                    <label for="vendor_part_number" style="display: inline-block; width: 150px;">Part Number:</label>
                                    <input type="text" name="vendor_part_number" id="vendor_part_number" value="<?php echo htmlspecialchars($vendor_part_number ?? ''); ?>" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="description" style="display: inline-block; width: 150px;">Description:</label>
                                    <input type="text" name="description" id="description" value="<?php echo htmlspecialchars($description ?? ''); ?>" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="quantity" style="display: inline-block; width: 150px;">Quantity:</label>
                                    <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($quantity ?? ''); ?>" min="1" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="unit_price" style="display: inline-block; width: 150px;">Unit Price:</label>
                                    <input type="number" step="0.01" name="unit_price" id="unit_price" value="<?php echo htmlspecialchars($unit_price ?? ''); ?>" min="0" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="status" style="display: inline-block; width: 150px;">Status:</label>
                                    <select name="status" id="status" required>
                                        <option value="">Select Status</option>
                                        <option value="ordered" <?php echo (isset($status) && $status === 'ordered') ? 'selected' : ''; ?>>Ordered</option>
                                        <option value="received" <?php echo (isset($status) && $status === 'received') ? 'selected' : ''; ?>>Received</option>
                                        <option value="installed" <?php echo (isset($status) && $status === 'installed') ? 'selected' : ''; ?>>Installed</option>
                                    </select>
                                </div>

                            </fieldset>

                            <button type="submit" class="fancy_button">Add Part</button>

                            <a href="view_select_vehicle.php" class="fancy_button" style="margin-left: 10px;">Back to Parts Order</a>
                        </form>
                    </div>

                </div>
            </div>

            <div class="clear"></div>
        </div>

        <?php include("lib/footer.php"); ?>
    </div>
</body>