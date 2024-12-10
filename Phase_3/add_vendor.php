<?php

include('lib/common.php');
// written by GTusername3

if ($_SESSION['user_permission_index'] != 1 && $_SESSION['user_permission_index'] != 4) {
    echo "Permission denied";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $vendor_name = mysqli_real_escape_string($db, $_POST['vendor_name'] ?? '');
    $address_street = mysqli_real_escape_string($db, $_POST['address_street'] ?? '');
    $address_city = mysqli_real_escape_string($db, $_POST['address_city'] ?? '');
    $address_state = mysqli_real_escape_string($db, $_POST['address_state'] ?? '');
    $address_postal = mysqli_real_escape_string($db, $_POST['address_postal'] ?? '');
    $phone_number = mysqli_real_escape_string($db, $_POST['phone_number'] ?? '');

    $error_msg = [];

    if (empty($vendor_name)) $error_msg[] = "Please enter a vendor name.";
    if (empty($address_street)) $error_msg[] = "Please enter a street address.";
    if (empty($address_city)) $error_msg[] = "Please enter a city.";
    if (empty($address_state)) $error_msg[] = "Please enter a state.";
    if (empty($address_postal)) $error_msg[] = "Please enter a postal code.";
    if (empty($phone_number)) $error_msg[] = "Please enter a phone number.";

    try {
        if (empty($error_msg)) {
            $queryVendor = "INSERT INTO Vendor (name, address_street, address_city, address_state, address_postal, phone_number)
                            VALUES ('$vendor_name', '$address_street', '$address_city', '$address_state', '$address_postal', '$phone_number')";
            
            if (mysqli_query($db, $queryVendor)) {
                $verifyQuery = "SELECT * FROM Vendor 
                                WHERE name = '$vendor_name' 
                                AND address_street = '$address_street' 
                                AND address_city = '$address_city' 
                                AND address_state = '$address_state' 
                                AND address_postal = '$address_postal' 
                                AND phone_number = '$phone_number'";
                $verifyResult = mysqli_query($db, $verifyQuery);
    
                if ($verifyResult && mysqli_num_rows($verifyResult) > 0) {
                    $row = mysqli_fetch_assoc($verifyResult);
                    $_SESSION['VendorID'] = $row['name'];
                    // header("Location: view_select_vehicle.php");
                    // exit();
                } else {
                    $error_msg = "Error: Vendor was not found in the database after insertion. Please try again.";
                    header("Location: add_vendor.php");
                    exit();
                }
            }
        }
    } catch (mysqli_sql_exception $e) {
        // Handle duplicate key check
        if (mysqli_errno($db) == 1062) { 
            $error_msg = "A vendor with this name already exists.";
            echo "<div style='color: red; font-weight: bold;'>A vendor with this name already exists. Please try a different name.</div>";   
        } else {
            $error_msg = "Failed to add vendor. SQL Error: " . $mysqli_error($db);
            echo "<div style='color: red; font-weight: bold;'>Failed to add vendor. SQL Error: " . htmlspecialchars(mysqli_error($db)) . "</div>";

        }
        header("Location: add_vendor.php");
        exit();
    }    
    
}


?>

<?php include("lib/header.php"); ?>
    <title>Add Customer</title>
</head>
<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="features">

                    <div class="profile_section">
                        <div class="subtitle">Add New Vendor</div>

                        <?php if (!empty($error_msg)): ?>
                            <div style="color: red; font-weight: bold; margin-bottom: 20px;">
                                <?php foreach ($error_msg as $msg): ?>
                                    <?php echo htmlspecialchars($msg); ?><br>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form name="addVendorForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                            <fieldset style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                <legend style="font-weight: bold;">Vendor Details</legend>

                                <div style="margin-bottom: 15px;">
                                    <label for="vendor_name" style="display: inline-block; width: 150px;">Vendor Name:</label>
                                    <input type="text" name="vendor_name" id="vendor_name" value="<?php echo htmlspecialchars($vendor_name ?? ''); ?>" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="address_street" style="display: inline-block; width: 150px;">Street Address:</label>
                                    <input type="text" name="address_street" id="address_street" value="<?php echo htmlspecialchars($address_street ?? ''); ?>" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="address_city" style="display: inline-block; width: 150px;">City:</label>
                                    <input type="text" name="address_city" id="address_city" value="<?php echo htmlspecialchars($address_city ?? ''); ?>" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="address_state" style="display: inline-block; width: 150px;">State:</label>
                                    <input type="text" name="address_state" id="address_state" value="<?php echo htmlspecialchars($address_state ?? ''); ?>" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="address_postal" style="display: inline-block; width: 150px;">Postal Code:</label>
                                    <input type="text" name="address_postal" id="address_postal" value="<?php echo htmlspecialchars($address_postal ?? ''); ?>" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="phone_number" style="display: inline-block; width: 150px;">Phone Number:</label>
                                    <input type="text" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($phone_number ?? ''); ?>" required>
                                </div>
                            </fieldset>

                            <button type="submit" class="fancy_button">Add Vendor</button>
                             <!-- add a go back button to add_parts_order -->
                        </form>
                    </div>

                    <!-- Add "Go Back" Button -->
                    <div class='profile_section'>
                        <form action="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'index.php'); ?>" method="get">
                            <button type="submit" class="fancy_button">Go Back</button>
                        </form>
                    </div>
                    
                </div>
            </div>

            <div class="clear"></div>
        </div>

        <?php include("lib/footer.php"); ?>
    </div>
</body>