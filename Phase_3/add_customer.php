<?php
include('lib/common.php');
// written by GTusername3

if ($_SESSION['user_permission_index'] != 1 && $_SESSION['user_permission_index'] != 2 && $_SESSION['user_permission_index'] != 4) { ##invt clerk 1/salespeople 2/owner 4
    echo "Permission denied";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address_street = mysqli_real_escape_string($db, $_POST['address_street'] ?? '');
    $address_city = mysqli_real_escape_string($db, $_POST['address_city'] ?? '');
    $address_state = mysqli_real_escape_string($db, $_POST['address_state'] ?? '');
    $address_postal = mysqli_real_escape_string($db, $_POST['address_postal'] ?? '');
    $phone_number = mysqli_real_escape_string($db, $_POST['phone_number'] ?? '');
    $email_address = mysqli_real_escape_string($db, $_POST['email_address'] ?? '');
    $customer_type = mysqli_real_escape_string($db, $_POST['customer_type'] ?? '');

    $error_msg = [];

    // Validate general fields
    if (empty($address_street)) $error_msg[] = "Please enter a street address.";
    if (empty($address_city)) $error_msg[] = "Please enter a city.";
    if (empty($address_state)) $error_msg[] = "Please enter a state.";
    if (empty($address_postal)) $error_msg[] = "Please enter a postal code.";
    if (empty($phone_number)) $error_msg[] = "Please enter a phone number.";
    if (empty($email_address)) $error_msg[] = "Please enter an email address.";
    if (empty($customer_type)) $error_msg[] = "Please select a customer type (Individual or Business).";

    if (empty($error_msg)) {
        if ($customer_type == 'Individual') {
            $first_name = mysqli_real_escape_string($db, $_POST['first_name'] ?? '');
            $last_name = mysqli_real_escape_string($db, $_POST['last_name'] ?? '');
            $SSN = mysqli_real_escape_string($db, $_POST['SSN'] ?? '');
            $customer_ID = $SSN;

            if (empty($first_name)) $error_msg[] = "Please enter a first name.";
            if (empty($last_name)) $error_msg[] = "Please enter a last name.";
            if (empty($SSN)) $error_msg[] = "Please enter an SSN.";

            if (empty($error_msg)) {
                // Check for duplicate
                $checkQuery = "SELECT customer_ID FROM Customer WHERE customer_ID = '$customer_ID'";
                $checkResult = mysqli_query($db, $checkQuery);

                if (mysqli_num_rows($checkResult) > 0) {
                    echo "<p style='color: red;'>Error: Customer with this SSN already exists.</p>";
                } else {
                    $queryCustomer = "INSERT INTO Customer (customer_ID, address_street, address_city, address_state, address_postal, phone_number, email_address) 
                                      VALUES ('$customer_ID', '$address_street', '$address_city', '$address_state', '$address_postal', '$phone_number', '$email_address')";
                    $resultCustomer = mysqli_query($db, $queryCustomer);

                    if ($resultCustomer) {
                        $queryIndividual = "INSERT INTO Individual (customer_ID, first_name, last_name, SSN) 
                                            VALUES ('$customer_ID', '$first_name', '$last_name', '$SSN')";
                        $resultIndividual = mysqli_query($db, $queryIndividual);

                        if ($resultIndividual) {
                            $_SESSION['Customer_ID'] = $customer_ID;
                            $success_msg = "Customer found! Customer ID: " . htmlspecialchars($row['customer_ID']);
                            // header("Location: add_vehicle.php");
                            // exit();
                        } else {
                            echo "<p style='color: red;'>Error adding individual details: " . htmlspecialchars(mysqli_error($db)) . "</p>";
                        }
                    } else {
                        echo "<p style='color: red;'>Error adding customer: " . htmlspecialchars(mysqli_error($db)) . "</p>";
                    }
                }
            }
        } elseif ($customer_type == 'Business') {
            $primary_contact_first_name = mysqli_real_escape_string($db, $_POST['primary_contact_first_name'] ?? '');
            $primary_contact_last_name = mysqli_real_escape_string($db, $_POST['primary_contact_last_name'] ?? '');
            $primary_contact_title = mysqli_real_escape_string($db, $_POST['primary_contact_title'] ?? '');
            $business_name = mysqli_real_escape_string($db, $_POST['business_name'] ?? '');
            $ITIN = mysqli_real_escape_string($db, $_POST['ITIN'] ?? '');
            $customer_ID = $ITIN;

            if (empty($business_name)) $error_msg[] = "Please enter the business name.";
            if (empty($ITIN)) $error_msg[] = "Please enter an ITIN.";

            if (empty($error_msg)) {
                // Check for duplicate
                $checkQuery = "SELECT customer_ID FROM Customer WHERE customer_ID = '$customer_ID'";
                $checkResult = mysqli_query($db, $checkQuery);

                if (mysqli_num_rows($checkResult) > 0) {
                    echo "<p style='color: red;'>Error: Customer with this ITIN already exists.</p>";
                } else {
                    $queryCustomer = "INSERT INTO Customer (customer_ID, address_street, address_city, address_state, address_postal, phone_number, email_address) 
                                      VALUES ('$customer_ID', '$address_street', '$address_city', '$address_state', '$address_postal', '$phone_number', '$email_address')";
                    $resultCustomer = mysqli_query($db, $queryCustomer);

                    if ($resultCustomer) {
                        $queryBusiness = "INSERT INTO Business (customer_ID, business_name, primary_contact_first_name, primary_contact_last_name, primary_contact_title, ITIN) 
                                          VALUES ('$customer_ID', '$business_name', '$primary_contact_first_name', '$primary_contact_last_name', '$primary_contact_title', '$ITIN')";
                        $resultBusiness = mysqli_query($db, $queryBusiness);

                        if ($resultBusiness) {
                            $_SESSION['Customer_ID'] = $customer_ID;
                            $success_msg = "Customer found! Customer ID: " . htmlspecialchars($row['customer_ID']);
                            // header("Location: add_vehicle.php");
                            // exit();
                        } else {
                            echo "<p style='color: red;'>Error adding business details: " . htmlspecialchars(mysqli_error($db)) . "</p>";
                        }
                    } else {
                        echo "<p style='color: red;'>Error adding customer: " . htmlspecialchars(mysqli_error($db)) . "</p>";
                    }
                }
            }
        }
    }
}
?>



<?php include("lib/header.php"); ?>
    <title>Add Customer</title>
</head>

<body>
    <div id="main_container">
          <?php include("lib/menu")?>
        <div class="center_content">
            <div class="center_left">
                <div class="features">

                    <div class="profile_section">
                        <div class="subtitle">Add New Customer</div>

                        <?php if (isset($success_msg)): ?>
                            <div style="color: green; font-weight: bold;"><?php echo $success_msg; ?></div>
                            <form action="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>" method="GET">
                                <button type="submit" class="fancy_button">Go Back</button>
                            </form>
                        <?php elseif (isset($_SESSION['error_msg'])): ?>
                            <div style="color: red; font-weight: bold;">
                                <?php echo htmlspecialchars($_SESSION['error_msg']); ?>
                            </div>
                        <?php endif; ?>

                        <form name="addCustomerForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                            <fieldset style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                <legend style="font-weight: bold;">Customer Details</legend>
                                <div style="margin-bottom: 15px;">
                                    <label for="customer_type" style="display: inline-block; width: 150px;">Customer Type:</label>
                                    <select name="customer_type" id="customer_type" required>
                                        <option value="" disabled selected>Select</option>
                                        <option value="Individual">Individual</option>
                                        <option value="Business">Business</option>
                                    </select>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="address_street" style="display: inline-block; width: 150px;">Street Address:</label>
                                    <input type="text" name="address_street" id="address_street" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="address_city" style="display: inline-block; width: 150px;">City:</label>
                                    <input type="text" name="address_city" id="address_city" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="address_state" style="display: inline-block; width: 150px;">State:</label>
                                    <input type="text" name="address_state" id="address_state" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="address_postal" style="display: inline-block; width: 150px;">Postal Code:</label>
                                    <input type="text" name="address_postal" id="address_postal" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="phone_number" style="display: inline-block; width: 150px;">Phone Number:</label>
                                    <input type="text" name="phone_number" id="phone_number" required>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="email_address" style="display: inline-block; width: 150px;">Email Address:</label>
                                    <input type="email" name="email_address" id="email_address" required>
                                </div>
                            </fieldset>

                            <!-- Individual -->
                            <fieldset id="individual_fields" style="display: none; border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                <legend style="font-weight: bold;">Individual Details</legend>
                                <div style="margin-bottom: 15px;">
                                    <label for="first_name" style="display: inline-block; width: 150px;">First Name:</label>
                                    <input type="text" name="first_name" id="first_name">
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="last_name" style="display: inline-block; width: 150px;">Last Name:</label>
                                    <input type="text" name="last_name" id="last_name">
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="SSN" style="display: inline-block; width: 150px;">SSN:</label>
                                    <input type="text" name="SSN" id="SSN">
                                </div>
                            </fieldset>

                            <!-- Business -->
                            <fieldset id="business_fields" style="display: none; border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                <legend style="font-weight: bold;">Business Details</legend>
                                <div style="margin-bottom: 15px;">
                                    <label for="business_name" style="display: inline-block; width: 150px;">Business Name:</label>
                                    <input type="text" name="business_name" id="business_name">
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="primary_contact_first_name" style="display: inline-block; width: 150px;">Primary Contact First Name:</label>
                                    <input type="text" name="primary_contact_first_name" id="primary_contact_first_name">
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="primary_contact_last_name" style="display: inline-block; width: 150px;">Primary Contact Last Name:</label>
                                    <input type="text" name="primary_contact_last_name" id="primary_contact_last_name">
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="primary_contact_title" style="display: inline-block; width: 150px;">Primary Contact Title:</label>
                                    <input type="text" name="primary_contact_title" id="primary_contact_title">
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="ITIN" style="display: inline-block; width: 150px;">ITIN:</label>
                                    <input type="text" name="ITIN" id="ITIN">
                                </div>
                            </fieldset>

                            <button type="submit" class="fancy_button">Add Customer</button>
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

            <?php include("lib/error.php"); ?>
            <div class="clear"></div>
        </div>

        <?php include("lib/footer.php"); ?>
    </div>

    <script>
        document.getElementById('customer_type').addEventListener('change', function () {
            var customerType = this.value;
            document.getElementById('individual_fields').style.display = customerType === 'Individual' ? 'block' : 'none';
            document.getElementById('business_fields').style.display = customerType === 'Business' ? 'block' : 'none';
        });
    </script>
</body>
</html>

