<?php
include('lib/common.php');
// written by GTusername3

if ($_SESSION['user_permission_index'] != 2 && $_SESSION['user_permission_index'] != 4) {
    echo "Permission denied";
    exit();
}


$CustomerID = $_SESSION['Customer_ID'] ?? null;
$error_msg = $_SESSION['error_msg'] ?? null;

unset($_SESSION['error_msg']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
    $VIN = $_SESSION['VIN'];
    // $VIN = mysqli_real_escape_string($db, $_POST['VIN']);
    $buyer_customer_ID = $CustomerID;
    $salespeople_username = mysqli_real_escape_string($db, $_POST['salespeople_username']);
    // $salespeople_username = $_SESSION['username'];
    $sale_date = mysqli_real_escape_string($db, $_POST['sale_date']);
    # $sale_price = TODO

    if (empty($VIN) || empty($buyer_customer_ID) || empty($salespeople_username) || empty($sale_date)) {
        echo "<p style='color: red;'>Error: All fields are required.</p>";
    } else {
        $query = "DELETE Buy (VIN, buyer_customer_ID, salespeople_username, sale_date)
            VALUES ('$VIN','$buyer_customer_ID','$salespeople_username', '$sale_date')";
        $result = mysqli_query($db, $query);

        if ($result) {
            echo "<p style='color: green;'>Vehicle sold successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error processing sale: " . htmlspecialchars(mysqli_error($db)) . "</p>";
        }
    }
}
?>

<?php include("lib/header.php"); ?>
<title>Vendor Search</title>
</head>

</head>
<body>
    <div id='main_container'>
    <?php include("lib/menu")?>
        
        <div class="center_content">
            <div class="center_left">
                <div class="features">
                    <div class="profile_section">

                        <div class="subtitle">Sale a Vehicle</div>
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
                        <form name="add_vehicle_form" action="sell_vehicle.php" method="POST">
                            <table>
                                <tr>
                                    <td class="item_label">Customer ID</td>
                                    <td><?php echo $CustomerID; ?></td>
                                </tr>
                                <tr>
                                    <td class="item_label">VIN</td>
                                    <td><input type="text" name="VIN" required /></td>
                                </tr>
                                <tr>
                                    <td class="item_label">Salespeople Username</td>
                                    <td><input type="text" name="keyword" /></td>
                                </tr>
                                <tr>
                                    <td class="item_label">Sale Date</td>
                                    <td><input type="date" name="sale_date" required /></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <input type="submit" value="Sale Vehicle" class="fancy_button" />
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