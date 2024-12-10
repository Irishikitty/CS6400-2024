<?php
if (isset($_GET['VIN']) || isset($_SESSION['VIN'])) {
    $VehicleVIN = $_GET['VIN'] ?? $_SESSION['VIN'];
    $_SESSION['VIN'] = $VehicleVIN;
    echo "<br>";

    echo "<h2>Seller Information</h2>";
    $query = 
    "SELECT CONCAT(Individual.first_name, ' ', Individual.last_name) As individual_name,
            CONCAT(Business.primary_contact_first_name, ' ', Business.primary_contact_last_name) As buisness_primary_name,
            Business.primary_contact_title, Business.business_name,
            CONCAT(Customer.address_street, ', ', Customer.address_city, ', ', Customer.address_state, ', ', Customer.address_postal) As address_street, 
            Customer.phone_number, Customer.email_address
    FROM Vehicle
    LEFT JOIN Individual ON Vehicle.seller_customer_ID = Individual.customer_ID
    LEFT JOIN Business ON Vehicle.seller_customer_ID = Business.customer_ID
    LEFT JOIN Customer ON Vehicle.seller_customer_ID = Customer.customer_ID
    WHERE Vehicle.VIN = '$VehicleVIN'
    ";
    $result = mysqli_query($db, $query);

    $query = 
    "SELECT CONCAT(Logged_in_User.first_name, ' ', Logged_in_User.last_name) As clerk_name
    FROM Vehicle
    LEFT JOIN Logged_in_User ON Vehicle.inventoryclerk_username = Logged_in_User.username
    WHERE Vehicle.VIN = '$VehicleVIN'
    ";
    $result2 = mysqli_query($db, $query);
    $clerkrow = mysqli_fetch_assoc($result2);

    // Fetch and display results
    if (mysqli_num_rows($result) > 0) {
        // Start the HTML table
        echo "<table border='1' cellpadding='5' cellspacing='0' style='width:80%';>";
        
        // Print table header (column names)
        echo "<tr>";
        $firstRow = mysqli_fetch_assoc($result);
        if (empty($firstRow['business_name'])){
            echo "<th colspan='3'>" . htmlspecialchars("Individual seller name") . "</th>";
            echo "</tr>";
            echo "<td colspan='3'>" . htmlspecialchars($firstRow['individual_name']) . "</td>";
            echo "</tr>";
            echo "<th colspan='3'>" . htmlspecialchars("Address") . "</th>";
            echo "</tr>";
            echo "<td colspan='3'>" . htmlspecialchars($firstRow['address_street']) . "</td>";
            echo "</tr>";
            echo "<th colspan='1'>" . htmlspecialchars("Phone number") . "</th>";
            echo "<th colspan='1'>" . htmlspecialchars("Email address") . "</th>";
            echo "<th colspan='1'>" . htmlspecialchars("Purchased by clerk") . "</th>";
            echo "</tr>";
            echo "<td colspan='1'>" . htmlspecialchars($firstRow['phone_number']) . "</td>";
            echo "<td colspan='1'>" . htmlspecialchars($firstRow['email_address']) . "</td>";
            echo "<td colspan='1'>" . htmlspecialchars($clerkrow['clerk_name']) . "</td>";
        } else {
            echo "<th>" . htmlspecialchars("Buisness seller name") . "</th>";
            echo "<th>" . htmlspecialchars("Primary contact title") . "</th>";
            echo "<th>" . htmlspecialchars("Primary name") . "</th>";
            echo "</tr>";
            echo "<td>" . htmlspecialchars($firstRow['business_name']) . "</td>";
            echo "<td>" . htmlspecialchars($firstRow['primary_contact_title']) . "</td>";
            echo "<td>" . htmlspecialchars($firstRow['buisness_primary_name']) . "</td>";
            echo "</tr>";
            echo "<th colspan='3'>" . htmlspecialchars("Address") . "</th>";
            echo "</tr>";
            echo "<td colspan='3'>" . htmlspecialchars($firstRow['address_street']) . "</td>";
            echo "</tr>";
            echo "<th colspan='1'>" . htmlspecialchars("Phone number") . "</th>";
            echo "<th colspan='1'>" . htmlspecialchars("Email address") . "</th>";
            echo "<th colspan='1'>" . htmlspecialchars("Purchased by clerk") . "</th>";
            echo "</tr>";
            echo "<td colspan='1'>" . htmlspecialchars($firstRow['phone_number']) . "</td>";
            echo "<td colspan='1'>" . htmlspecialchars($firstRow['email_address']) . "</td>";
            echo "<td colspan='1'>" . htmlspecialchars($clerkrow['clerk_name']) . "</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

    $query = 
    "SELECT Buy.sale_date ,
            CONCAT(Individual.first_name, ' ', Individual.last_name) As individual_name,
            CONCAT(Business.primary_contact_first_name, ' ', Business.primary_contact_last_name) As buisness_primary_name,
            Business.primary_contact_title, Business.business_name, 
            Customer.phone_number, Customer.email_address,
            CONCAT(Customer.address_street, ', ', Customer.address_city, ', ', Customer.address_state, ', ', Customer.address_postal) As address_street,
            CONCAT(Logged_in_User.first_name, ' ', Logged_in_User.last_name) As sales_name
    FROM Buy
    LEFT JOIN Individual ON Buy.buyer_customer_ID = Individual.customer_ID
    LEFT JOIN Business ON Buy.buyer_customer_ID = Business.customer_ID
    LEFT JOIN Customer ON Buy.buyer_customer_ID = Customer.customer_ID
    LEFT JOIN Logged_in_User ON Buy.salespeople_username = Logged_in_User.username
    WHERE Buy.VIN = '$VehicleVIN'
    ";
    $result = mysqli_query($db, $query);
    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Buyer Information</h2>";
        // Start the HTML table
        echo "<table border='1' cellpadding='5' cellspacing='0' style='width:80%';>";
        // Print table header (column names)
        echo "<tr>";
        $firstRow = mysqli_fetch_assoc($result);
        if (empty($firstRow['business_name'])){
            echo "<th colspan='3'>" . htmlspecialchars("Individual seller name") . "</th>";
            echo "</tr>";
            echo "<td colspan='3'>" . htmlspecialchars($firstRow['individual_name']) . "</td>";
            echo "</tr>";
            echo "<th colspan='3'>" . htmlspecialchars("Address") . "</th>";
            echo "</tr>";
            echo "<td colspan='3'>" . htmlspecialchars($firstRow['address_street']) . "</td>";
            echo "</tr>";
            echo "<th colspan='1'>" . htmlspecialchars("Phone number") . "</th>";
            echo "<th colspan='1'>" . htmlspecialchars("Email address") . "</th>";
            echo "<th colspan='1'>" . htmlspecialchars("Sold by Salespeople") . "</th>";
            echo "</tr>";
            echo "<td colspan='1'>" . htmlspecialchars($firstRow['phone_number']) . "</td>";
            echo "<td colspan='1'>" . htmlspecialchars($firstRow['email_address']) . "</td>";
            echo "<td colspan='1'>" . htmlspecialchars($firstRow['sales_name']) . "</td>";
        } else {
            echo "<th>" . htmlspecialchars("Buisness seller name") . "</th>";
            echo "<th>" . htmlspecialchars("Primary contact title") . "</th>";
            echo "<th>" . htmlspecialchars("Primary name") . "</th>";
            echo "</tr>";
            echo "<td>" . htmlspecialchars($firstRow['business_name']) . "</td>";
            echo "<td>" . htmlspecialchars($firstRow['primary_contact_title']) . "</td>";
            echo "<td>" . htmlspecialchars($firstRow['buisness_primary_name']) . "</td>";
            echo "</tr>";
            echo "<th colspan='3'>" . htmlspecialchars("Address") . "</th>";
            echo "</tr>";
            echo "<td colspan='3'>" . htmlspecialchars($firstRow['address_street']) . "</td>";
            echo "</tr>";
            echo "<th colspan='1'>" . htmlspecialchars("Phone number") . "</th>";
            echo "<th colspan='1'>" . htmlspecialchars("Email address") . "</th>";
            echo "<th colspan='1'>" . htmlspecialchars("Sold by Salespeople") . "</th>";
            echo "</tr>";
            echo "<td colspan='1'>" . htmlspecialchars($firstRow['phone_number']) . "</td>";
            echo "<td colspan='1'>" . htmlspecialchars($firstRow['email_address']) . "</td>";
            echo "<td colspan='1'>" . htmlspecialchars($firstRow['sales_name']) . "</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

} else {
    echo "<p>Error: Missing VIN parameter.</p>";
}

?>