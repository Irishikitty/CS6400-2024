<?php

include('lib/common.php');
// written by GTusername3

if ($_SESSION['user_permission_index'] != 1 && $_SESSION['user_permission_index'] != 2 && $_SESSION['user_permission_index'] != 4) { ##invt clerk 1/salespeople 2/owner 4
    echo "Permission denied";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enteredSSN = mysqli_real_escape_string($db, $_POST['SSN'] ?? '');
    $enteredITIN = mysqli_real_escape_string($db, $_POST['ITIN'] ?? '');

    if (!empty($enteredSSN)) {
        $query = "
            SELECT 
                i.customer_ID
            FROM
                Individual i
            WHERE
                i.SSN = '$enteredSSN';
        ";
    } elseif (!empty($enteredITIN)) {
        $query = "
            SELECT 
                b.customer_ID
            FROM
                Business b
            WHERE
                b.ITIN = '$enteredITIN';
        ";
    } else {
        $error_msg = "Please enter either an SSN or an ITIN";
    }

    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['Customer_ID'] = $row['customer_ID'];
        // header("Location: add_vehicle.php");
        // exit();
    } else {
        $_SESSION['error_msg'] = "No results found.";
        header("Location: search_customer.php");
        exit();
    }
}

?>

<?php include("lib/header.php"); ?>
    <title>Customer Search</title>
</head>
<body>
    <div id="main_container">
    <?php include("lib/menu.php"); ?>

        <div class="center_content">
            <div class="center_left">
                <div class="features">
                    <div class="profile_section">
                        <div class="subtitle">Search for Customers</div>
                        <form name="searchform" action="search_customer.php" method="POST">
                            <table>
                                <tr>
                                    <td class="item_label">SSN</td>
                                    <td><input type="text" name="SSN" /></td>
                                </tr>
                                <tr>
                                    <td class="item_label">ITIN</td>
                                    <td><input type="text" name="ITIN" /></td>
                                </tr>
                            </table>
                            <input type="submit" value="Search" class="fancy_button">
                        </form>
                    </div>

                    <div class='profile_section'>
                        <div class='subtitle'>Search Results</div>
                        <table>
                            <tr>
                                <td class='heading'>Customer ID</td>
                            </tr>
                            <?php
                                if (isset($result) && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['customer_ID']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr>";
                                    echo "<td colspan='6' style='color: red; font-weight: bold;'>";
                                    echo htmlspecialchars($_SESSION['error_msg'] ?? "No results found. Please try again.");
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </table>
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
    </div>
</body>
</html>