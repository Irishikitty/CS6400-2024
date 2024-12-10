<?php

include('lib/common.php');
// written by GTusername4

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['filter'])) {
    
	$VehicleVIN = mysqli_real_escape_string($db, $_POST['VIN']);

	// $query = "SELECT 
    // Vehicle.VIN, Vehicle.vehicle_type, Vehicle.manufacturer_name, 
    // Vehicle.model_name, Vehicle.model_year, Vehicle.fuel_type,
    // Vehicle.horsepower, Vehicle.sale_price
    // GROUP_CONCAT(Vehicle_color.color ORDER BY Vehicle_color.color SEPARATOR ',')
    // FROM `Vehicle` LEFT JOIN Vehicle_color ON Vehicle.VIN = Vehicle_color.VIN
    // WHERE (Vehicle.VIN=$VIN)
    // GROUP BY Vehicle.VIN";
    $query1 = "SELECT 
            Vehicle.VIN, Vehicle.vehicle_type, Vehicle.manufacturer_name, Vehicle.model_name,
            Vehicle.model_year, Vehicle.fuel_type, Vehicle.horsepower, Vehicle.sale_price,
            GROUP_CONCAT(Vehicle_color.color ORDER BY Vehicle_color.color SEPARATOR ',') AS vehicle_colors
            FROM Vehicle INNER JOIN Vehicle_color on Vehicle.VIN = Vehicle_color.VIN
            WHERE (UPPER(Vehicle.VIN)=UPPER('$VehicleVIN') or '$VehicleVIN'='')
            GROUP BY Vehicle.VIN
            ORDER BY Vehicle.VIN ASC
            ";
    $query2 = " WITH FilterVehicles AS (
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

    $query3 = " WITH FilterVehicles AS (
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
            -- LEFT JOIN Buy ON FilterVehicles.VIN = Buy.VIN
            WHERE NOT EXISTS ( 
            SELECT 1
            FROM Buy
            WHERE Buy.VIN = FilterVehicles.VIN
            );";

            // $veh_result = mysqli_query($db, $query2);
            // $count = mysqli_num_rows($veh_result);
            // echo $count;
            $_SESSION['user_permission_index'] = 4;
        if ($_SESSION['user_permission_index']>2) {// manager=3 or owner=4
            $query = $query1;
            $veh_result = mysqli_query($db, $query);
            $count = mysqli_num_rows($veh_result);
        // echo $count;
        } else if ($_SESSION['user_permission_index']==1) { // inventory clerk=1
            $query = $query3;
            $veh_result = mysqli_query($db, $query);
            $count = mysqli_num_rows($veh_result);
        } else { // public user = 0, salespeople =2
            $query = $query2;
            $veh_result = mysqli_query($db, $query);
            $count = mysqli_num_rows($veh_result);
        }
        include('lib/show_queries.php');
        if (mysqli_affected_rows($db) == -1) {
            array_push($error_msg,  "SELECT ERROR:Failed to find vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
        }
}
?>

<?php include("lib/header.php"); ?>
<!-- Setting header -->
<title>North Avenue Automative Search Vehicle</title>
</head>
	
<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>
        <div class="center_content">	
            <div class="center_left">
                <div class="features">   
                    <div class="vehicle_vin_section">
                        <div class="subtitle">Search Vehicle by VIN</div>   
                        
                        <form name="vin_form" action="search_vehicle_by_VIN.php" method="POST">
                            <table>
                                <tr>
                                    <td class="item_label">VIN</td>
                                    <td>
                                        <input type="text" name="VIN" id="VIN" oninput="validateVIN()" />
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
                            </table>
                            <a href="javascript:vin_form.submit();" class="fancy_button">Search</a>
                        </form>
                    </div>

                    <div class='profile_section'>
                        <div class='subtitle'>Search Results</div>
                        <table>
                            <tr>
                                <td class='heading'>VIN</td>
                                <td class='heading'>Vehicle type</td>
                                <td class='heading'>Manufacturer</td>
                                <td class='heading'>Model</td>
                                <td class='heading'>Year</td>
                                <td class='heading'>Fuel type</td>
                                <td class='heading'>Colors</td>
                                <td class='heading'>Horespower</td>
                                <td class='heading'>Sale Price</td>
                            </tr>
                                <?php
                                    if (isset($veh_result)) {
                                        if ($count>0) {
                                            while ($row = mysqli_fetch_array($veh_result, MYSQLI_ASSOC)){
                                                $VIN = urlencode($row['VIN']);
                                                print "<tr>";
                                                print "<td><a href='view_select_vehicle.php?VIN=$VIN'>{$row['VIN']}</a></td>";
                                                print "<td>{$row['vehicle_type']}</td>";
                                                print "<td>{$row['manufacturer_name']}</td>";
                                                print "<td>{$row['model_name']}</td>";
                                                print "<td>{$row['model_year']}</td>";
                                                print "<td>{$row['fuel_type']}</td>";
                                                print "<td>{$row['vehicle_colors']}</td>";
                                                print "<td>{$row['horsepower']}</td>";		
                                                print "<td>{$row['sale_price']}</td>";						
                                                print "</tr>";
                                            }
                                        }
                                        else {
                                            echo "<tr><td colspan='9'>Sorry, it looks like we don’t have that in stock!</td></tr>";
                                        }
                                    } 
                                ?>
                            </table>
                        </div>
                    </div> 
                </div> 	
            </div> 
            <?php include("lib/error.php"); ?> 
            <div class="clear"></div> 		
            <?php include("lib/footer.php"); ?>
        </div>   
    </div>
</body>
</html>