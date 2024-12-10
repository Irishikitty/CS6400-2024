<?php

include('lib/common.php');
// written by GTusername3

if ($_SESSION['user_permission_index'] != 1 && $_SESSION['user_permission_index'] != 4) {
    echo "Permission denied";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $VendorID = mysqli_real_escape_string($db, trim($_POST['VendorID']));

    if (!empty($VendorID)) {
        $query = "SELECT name FROM Vendor WHERE LOWER(name) = LOWER('$VendorID')";    
        $result = mysqli_query($db, $query);

        if (!$result) {
            echo "<p style='color: red;'>Error in query: " . mysqli_error($db) . "</p>";
        }

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['VendorID'] = $row['name'];
            // header("Location: view_select_vehicle.php");
            // exit();
        } else {
            $_SESSION['error_msg'] = "No results found. Please check the Vendor Name.";
            header("Location: search_vendor.php");
            exit();
        }
    } else {
        $_SESSION['error_msg'] = "Please enter a vendor name.";
        header("Location: search_vendor.php");
        exit();
    }
}

?>

<?php include("lib/header.php"); ?>
    <title>Vendor Search</title>
</head>
<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>
        <div class="center_content">
            <div class="center_left">
                <div class="features">
                    <div class="profile_section">
                        <div class="subtitle">Search for Vendors</div>
                        <form name="searchform" action="search_vendor.php" method="POST">
                            <table>
                                <tr>
                                    <td class="item_label">Vendor Name</td>
                                    <td><input type="text" name="VendorID" /></td>
                                </tr>
                            </table>
                            <input type="submit" value="Search" class="fancy_button">
                        </form>
                    </div>

                    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                    <div class='profile_section'>
                        <div class='subtitle'>Search Results</div>
                        <table>
                            <tr>
                                <td class='heading'>Vendor Name</td>
                            </tr>
                            <?php
                                if (isset($result) && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr>";
                                    echo "<td colspan='1' style='color: red; font-weight: bold;'>";
                                    echo htmlspecialchars($_SESSION['error_msg'] ?? "No results found. Please try again.");
                                    unset($_SESSION['error_msg']); // Clear error message after display
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </table>
                    </div>
                    <?php endif; ?>
                    
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
