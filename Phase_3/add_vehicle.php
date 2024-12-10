<?php
include('lib/common.php');
// written by GTusername3

if ($_SESSION['user_permission_index'] != 1 && $_SESSION['user_permission_index'] != 4) {
    echo "<p style='color: red;'>Permission denied</p>";
    exit();
}

$CustomerID = $_SESSION['Customer_ID'] ?? null;
$error_msg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['error_msg']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Retrieve and sanitize inputs
    $VIN = mysqli_real_escape_string($db, $_POST['VIN']);
    $model_name = mysqli_real_escape_string($db, $_POST['model_name']);
    $model_year = (int)$_POST['model_year'];
    $fuel_type = mysqli_real_escape_string($db, $_POST['fuel_type']);
    $horsepower = (int)$_POST['horsepower'];
    $description = !empty($_POST['description']) ? "'" . mysqli_real_escape_string($db, $_POST['description']) . "'" : "NULL";
    $purchase_price = (float)$_POST['purchase_price'];
    $sale_price = round(1.25 * $purchase_price, 2); 
    $vehicle_condition = mysqli_real_escape_string($db, $_POST['vehicle_condition']);
    $purchase_date = mysqli_real_escape_string($db, $_POST['purchase_date']);
    $vehicle_type = mysqli_real_escape_string($db, $_POST['vehicle_type']);
    $manufacturer_name = mysqli_real_escape_string($db, $_POST['manufacturer_name']);
    $inventoryclerk_username = mysqli_real_escape_string($db, $_SESSION['username']);
    $colors = $_POST['colors'] ?? []; // Handle multiple colors

    // Validate purchase date
    if ($purchase_date > date('Y-m-d')) {
        echo "<p style='color: red;'>Error: Purchase date cannot exceed the current date.</p>";
        exit();
    }

    // Ensure a customer is selected
    if (!$CustomerID) {
        echo "<p style='color: red;'>Error: No customer found. Please select a customer first.</p>";
        exit();
    }

    // Insert the vehicle into the database
    $query = "
        INSERT INTO Vehicle (
            VIN, model_name, model_year, fuel_type, horsepower, description,
            sale_price, purchase_price, vehicle_condition, purchase_date,
            seller_customer_ID, inventoryclerk_username, vehicle_type, manufacturer_name
        ) VALUES (
            '$VIN', '$model_name', $model_year, '$fuel_type', $horsepower, $description,
            $sale_price, $purchase_price, '$vehicle_condition', '$purchase_date',
            '$CustomerID', '$inventoryclerk_username', '$vehicle_type', '$manufacturer_name'
        )
    ";

    $result = mysqli_query($db, $query);

    if ($result) {
        foreach ($colors as $color) {
            $colorEscaped = mysqli_real_escape_string($db, $color);
            $colorQuery = "
                INSERT INTO Vehicle_color (VIN, color)
                VALUES ('$VIN', '$colorEscaped')
            ";
            if (!mysqli_query($db, $colorQuery)) {
                echo "<p style='color: red;'>Error adding color '$color': " . mysqli_error($db) . "</p>";
            }
        }

        echo "<p style='color: green;'>Vehicle and colors added successfully!</p>";
        unset($_SESSION['Customer_ID']);
        header("Location: view_select_vehicle.php?VIN=" . urlencode($VIN));
        exit();
    } else {
        echo "<p style='color: red;'>Error adding vehicle: " . htmlspecialchars(mysqli_error($db)) . "</p>";
        exit();
    }
}
?>


<?php include("lib/header.php"); ?>
<title>Vendor Search</title>
</head>

</head>
<body>
    <div id='main_container'>
        <?php include("lib/menu.php"); ?>
        <div class="center_content">
            <div class="center_left">
                <div class="features">
                    <div class="profile_section">

                        <div class="subtitle">Add a Vehicle</div>
                        <div>
                            <form action="search_customer.php" method="GET" style="display: inline;">
                                <button type="submit" class="fancy_button">Search for Customer</button>
                            </form>
                            <form action="add_customer.php" method="GET" style="display: inline;">
                                <button type="submit" class="fancy_button">Add a New Customer</button>
                            </form>
                        </div>
                        <br />

                        <?php if (!empty($CustomerID)): ?>
                        <form name="add_vehicle_form" action="add_vehicle.php" method="POST">
                            <table>
                                <tr>
                                    <td class="item_label">Customer ID</td>
                                    <td><?php echo $CustomerID; ?></td>
                                </tr>
                                <tr>
                                    <td class="item_label">VIN</td>
                                    <!-- <td><input type="text" name="VIN" required /></td> -->
                                    <td>
                                        <input type="text" name="VIN" id="VIN" required oninput="validateVIN()" />
                                        <span id="vin-error" style="color: red;"></span>
                                    </td>

                                    <script>
                                        function validateVIN() {
                                            const vinInput = document.getElementById('VIN');
                                            const errorSpan = document.getElementById('vin-error');
                                            const vinPattern = /^[0-9A-Za-z]+$/; // Only numbers and letters
                                            
                                            if (!vinPattern.test(vinInput.value)) {
                                                errorSpan.textContent = "Only numbers and letters are allowed.";
                                            } else {
                                                errorSpan.textContent = ""; // Clear the error message
                                            }
                                        }
                                    </script>
                                </tr>
                                <tr>
                                    <td class="item_label">Model Name</td>
                                    <!-- <td><input type="text" name="model_name" required /></td> -->
                                    <td>
                                        <input type="text" name="model_name" id="VIN" required oninput="validateVIN()" />
                                        <span id="vin-error" style="color: red;"></span>
                                    </td>

                                    <script>
                                        function validateVIN() {
                                            const vinInput = document.getElementById('VIN');
                                            const errorSpan = document.getElementById('vin-error');
                                            const vinPattern = /^[0-9A-Za-z]+$/; // Only numbers and letters
                                            
                                            if (!vinPattern.test(vinInput.value)) {
                                                errorSpan.textContent = "Only numbers and letters are allowed.";
                                            } else {
                                                errorSpan.textContent = ""; // Clear the error message
                                            }
                                        }
                                    </script>
                                </tr>
                                <tr>
                                    <td class="item_label">Year</td>
                                    <td>
                                        <select name="model_year" required>
                                            <option value="">Select a Year</option>
                                            <?php
                                            $currentYear = date('Y');
                                            for ($year = $currentYear; $year >= 1980; $year--) {
                                                echo "<option value='$year'>$year</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Fuel Type</td>
                                    <td>
                                        <select name="fuel_type" required>
                                            <option value="">Select a Fuel Type</option>
                                            <?php
                                            $result = mysqli_query($db, "SELECT DISTINCT fuel_type FROM Vehicle");
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<option value='{$row['fuel_type']}'>{$row['fuel_type']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Horsepower</td>
                                    <td><input type="number" name="horsepower" min="0" required /></td>
                                </tr>
                                <tr>
                                    <td class="item_label">Description</td>
                                    <td><textarea name="description"></textarea></td>
                                </tr>
                                <tr>
                                    <td class="item_label">Purchase Price</td>
                                    <td><input type="number" step="0.01" name="purchase_price" required /></td>
                                </tr>
                                <tr>
                                    <td class="item_label">Condition</td>
                                    <td>
                                        <select name="vehicle_condition" required>
                                            <option value="">Select Condition</option>
                                            <option value="Excellent">Excellent</option>
                                            <option value="Very Good">Very Good</option>
                                            <option value="Good">Good</option>
                                            <option value="Fair">Fair</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Vehicle Type</td>
                                    <td>
                                        <select name="vehicle_type" required>
                                            <option value="">Select a Vehicle Type</option>
                                            <?php
                                            $result = mysqli_query($db, "SELECT vehicle_type FROM Vehicle_Type");
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<option value='{$row['vehicle_type']}'>{$row['vehicle_type']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Manufacturer</td>
                                    <td>
                                        <select name="manufacturer_name" required>
                                            <option value="">Select a Manufacturer</option>
                                            <?php
                                            $result = mysqli_query($db, "SELECT manufacturer_name FROM Manufacturer_Name");
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<option value='{$row['manufacturer_name']}'>{$row['manufacturer_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="item_label">Color</td>
                                    <td>
                                        <select name="colors[]" multiple required>
                                            <option value="">Select Colors</option>
                                            <?php
                                            $result = mysqli_query($db, "SELECT color FROM Colors");
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<option value='{$row['color']}'>{$row['color']}</option>";
                                            }
                                            ?>
                                        </select>
                                        <p style="font-size: 0.9em; color: gray;">Hold down Ctrl (Windows) or Command (Mac) to select multiple colors.</p>
                                    </td>
                                </tr>

                                <!-- <tr>
                                    <td class="item_label">Keyword</td>
                                    <td><input type="text" name="keyword" /></td>
                                </tr> -->
                                <tr>
                                <td class="item_label">Purchase Date</td>
                                <td>
                                    <input type="date" id="purchase_date" name="purchase_date" required oninput="validatePurchaseDate()" />
                                    <span id="purchase-date-error" style="color: red;"></span>
                                </td>
                                <script>
                                    function validatePurchaseDate() {
                                        const dateInput = document.getElementById('purchase_date');
                                        const errorSpan = document.getElementById('purchase-date-error');
                                        const selectedDate = new Date(dateInput.value);
                                        const currentDate = new Date();

                                        // Check if the selected date exceeds the current date
                                        if (selectedDate > currentDate) {
                                            errorSpan.textContent = "Error: Purchase date cannot exceed the current date.";
                                        } else {
                                            errorSpan.textContent = ""; // Clear the error message
                                        }
                                    }
                                </script>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <input type="submit" value="Add Vehicle" class="fancy_button" />
                                    </td>
                                </tr>
                            </table>
                        </form>
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