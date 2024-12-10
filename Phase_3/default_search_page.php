<?php

include('lib/common.php');
// written by GTusername4
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['filter'])) {
	$VehicleType = mysqli_real_escape_string($db, $_POST['vehicle_type']);
	$VehicleManufacturer = mysqli_real_escape_string($db, $_POST['manufacturer_name']);
	$VehicleYear = mysqli_real_escape_string($db, $_POST['model_year']);
    $VehicleFuel = mysqli_real_escape_string($db, $_POST['fuel_type']);
    $VehicleColor = mysqli_real_escape_string($db, $_POST['color']);
    $Keyword = mysqli_real_escape_string($db, $_POST['keyword']);
	// $query = "SELECT 
    //             VIN, vehicle_type, manufacturer_name, model_name, 
    //             model_year, fuel_type, horsepower, sale_price,
    //             GROUP_CONCAT(Vehicle_color.color ORDER BY Vehicle_color.color SEPARATOR ',')
    //         FROM `Vehicle` INNER JOIN Vehicle_color on Vehicle.VIN = Vehicle_color.VIN
    //         WHERE (Vehicle.vehicle_type=$VehicleType or $VehicleType IS NULL)
    //         AND (Vehicle.manufacturer_name=$VehicleManufacturer or $VehicleManufacturer IS NULL)
    //         AND (Vehicle.model_year=$VehicleYear or $VehicleYear IS NULL)
    //         AND (Vehicle.fuel_type=$VehicleFuel or $VehicleFuel IS NULL)
    //         AND (($Keyword IS NULL) 
    //             OR Vehicle.vehicle_type LIKE CONCAT('%', $Keyword, '%'))
    //             OR Vehicle.manufacturer_name LIKE CONCAT('%', $Keyword, '%'))
    //             OR Vehicle.model_year LIKE CONCAT('%', $Keyword, '%'))
    //             OR Vehicle.description LIKE CONCAT('%', $Keyword, '%'))
    //         HAVING SUM(CASE WHEN Vehicle_color.color=$VehicleColor Then 1 Else 0 END) > 0
    //         ORDER BY VIN ASC;";
    $query1 = " SELECT 
            Vehicle.VIN, Vehicle.vehicle_type, Vehicle.manufacturer_name, Vehicle.model_name,
            Vehicle.model_year, Vehicle.fuel_type, Vehicle.horsepower, 
            Vehicle.sale_price, Vehicle.description,
            GROUP_CONCAT(Vehicle_color.color ORDER BY Vehicle_color.color SEPARATOR ',') AS vehicle_colors
            FROM Vehicle INNER JOIN Vehicle_color on Vehicle.VIN = Vehicle_color.VIN
            WHERE Vehicle.VIN IN (
            SELECT DISTINCT Vehicle.VIN
            FROM Vehicle
            LEFT JOIN Vehicle_color ON Vehicle.VIN = Vehicle_color.VIN
            WHERE (Vehicle.vehicle_type='$VehicleType' OR '$VehicleType'='')
            AND (Vehicle.manufacturer_name='$VehicleManufacturer' OR '$VehicleManufacturer'='')
            AND (Vehicle.model_year='$VehicleYear' OR '$VehicleYear'='')
            AND (Vehicle.fuel_type='$VehicleFuel' OR '$VehicleFuel'='')
            AND (Vehicle_color.color='$VehicleColor' OR '$VehicleColor'='')
            AND (
                ('$Keyword' = '') 
                OR LOWER(Vehicle.vehicle_type) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.manufacturer_name) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.model_year) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.description) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle_color.color) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                )
            )
            GROUP BY Vehicle.VIN
            ORDER BY Vehicle.VIN ASC";

    $query2 = " WITH FilterVehicles AS (
            SELECT 
            Vehicle.VIN, Vehicle.vehicle_type, Vehicle.manufacturer_name, Vehicle.model_name,
            Vehicle.model_year, Vehicle.fuel_type, Vehicle.horsepower, 
            Vehicle.sale_price, Vehicle.description,
            GROUP_CONCAT(Vehicle_color.color ORDER BY Vehicle_color.color SEPARATOR ',') AS vehicle_colors
            FROM Vehicle INNER JOIN Vehicle_color on Vehicle.VIN = Vehicle_color.VIN
            WHERE Vehicle.VIN IN (
            SELECT DISTINCT Vehicle.VIN
            FROM Vehicle
            LEFT JOIN Vehicle_color ON Vehicle.VIN = Vehicle_color.VIN
            WHERE (Vehicle.vehicle_type='$VehicleType' OR '$VehicleType'='')
            AND (Vehicle.manufacturer_name='$VehicleManufacturer' OR '$VehicleManufacturer'='')
            AND (Vehicle.model_year='$VehicleYear' OR '$VehicleYear'='')
            AND (Vehicle.fuel_type='$VehicleFuel' OR '$VehicleFuel'='')
            AND (Vehicle_color.color='$VehicleColor' OR '$VehicleColor'='')
            AND (
                ('$Keyword' = '') 
                OR LOWER(Vehicle.vehicle_type) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.manufacturer_name) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.model_year) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.description) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle_color.color) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                )
            )
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
            Vehicle.model_year, Vehicle.fuel_type, Vehicle.horsepower, 
            Vehicle.sale_price, Vehicle.description,
            GROUP_CONCAT(Vehicle_color.color ORDER BY Vehicle_color.color SEPARATOR ',') AS vehicle_colors
            FROM Vehicle INNER JOIN Vehicle_color on Vehicle.VIN = Vehicle_color.VIN
            WHERE Vehicle.VIN IN (
            SELECT DISTINCT Vehicle.VIN
            FROM Vehicle
            LEFT JOIN Vehicle_color ON Vehicle.VIN = Vehicle_color.VIN
            WHERE (Vehicle.vehicle_type='$VehicleType' OR '$VehicleType'='')
            AND (Vehicle.manufacturer_name='$VehicleManufacturer' OR '$VehicleManufacturer'='')
            AND (Vehicle.model_year='$VehicleYear' OR '$VehicleYear'='')
            AND (Vehicle.fuel_type='$VehicleFuel' OR '$VehicleFuel'='')
            AND (Vehicle_color.color='$VehicleColor' OR '$VehicleColor'='')
            AND (
                ('$Keyword' = '') 
                OR LOWER(Vehicle.vehicle_type) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.manufacturer_name) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.model_year) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.description) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle_color.color) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                )
            )
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

    $query4 = " WITH FilterVehicles AS (
            SELECT 
            Vehicle.VIN, Vehicle.vehicle_type, Vehicle.manufacturer_name, Vehicle.model_name,
            Vehicle.model_year, Vehicle.fuel_type, Vehicle.horsepower, 
            Vehicle.sale_price, Vehicle.description,
            GROUP_CONCAT(Vehicle_color.color ORDER BY Vehicle_color.color SEPARATOR ',') AS vehicle_colors
            FROM Vehicle INNER JOIN Vehicle_color on Vehicle.VIN = Vehicle_color.VIN
            WHERE Vehicle.VIN IN (
            SELECT DISTINCT Vehicle.VIN
            FROM Vehicle
            LEFT JOIN Vehicle_color ON Vehicle.VIN = Vehicle_color.VIN
            WHERE (Vehicle.vehicle_type='$VehicleType' OR '$VehicleType'='')
            AND (Vehicle.manufacturer_name='$VehicleManufacturer' OR '$VehicleManufacturer'='')
            AND (Vehicle.model_year='$VehicleYear' OR '$VehicleYear'='')
            AND (Vehicle.fuel_type='$VehicleFuel' OR '$VehicleFuel'='')
            AND (Vehicle_color.color='$VehicleColor' OR '$VehicleColor'='')
            AND (
                ('$Keyword' = '') 
                OR LOWER(Vehicle.vehicle_type) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.manufacturer_name) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.model_year) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle.description) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                OR LOWER(Vehicle_color.color) LIKE CONCAT('%', LOWER('$Keyword'), '%')
                )
            )
            GROUP BY Vehicle.VIN
            ORDER BY Vehicle.VIN ASC
            ) 
            SELECT FilterVehicles.VIN, FilterVehicles.vehicle_type, FilterVehicles.manufacturer_name, FilterVehicles.model_name,
            FilterVehicles.model_year, FilterVehicles.fuel_type, FilterVehicles.horsepower, 
            FilterVehicles.sale_price, FilterVehicles.description, FilterVehicles.vehicle_colors,
            Buy.buyer_customer_ID as buy
            FROM FilterVehicles
            LEFT JOIN Buy ON FilterVehicles.VIN = Buy.VIN;";
            // WHERE (Buy.VIN = FilterVehicles.VIN);";

        // $veh_result = mysqli_query($db, $query2);
        // $count = mysqli_num_rows($veh_result);
        // echo $count;
        // $_SESSION['user_permission_index'] = 3;
    if ($_SESSION['user_permission_index']>2) {// manager=3 or owner=4
        // $query = $query1;
        // $veh_result = mysqli_query($db, $query);
        // $count = mysqli_num_rows($veh_result);
        $query = $query4;
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

    $result_array = array();
    while ($row = mysqli_fetch_array($veh_result, MYSQLI_ASSOC)){
        $VIN = urlencode($row['VIN']);
        $result_array[] = $row;
        // echo $row['vehicle_type'];
    }
    $_SESSION['List_Vehicle'] = $result_array;

    include('lib/show_queries.php');

    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "SELECT ERROR:Failed to find vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
    }
}
?>


<?php include("lib/header.php"); ?>
<!-- Setting header -->
<title>North Avenue Automative</title>
</head>
	
<body>
    <div id="main_container">
        <?php include("lib/menu.php"); ?>
        <div class="center_content">
            <?php 
            $user_titles = [
                0 => 'User',
                1 => 'Clerk',
                2 => 'Sales',
                3 => 'Manager',
                4 => 'Owner'
            ];
            // Set the user title based on the permission index
            $_SESSION['user_title'] = isset($user_titles[$_SESSION['user_permission_index']]) ? $user_titles[$_SESSION['user_permission_index']] : 'Guest';
            echo "&nbsp;&nbsp;&nbsp;Welcome, " . htmlspecialchars($_SESSION['user_title']);
            ?>
            <div class="center_left">
                <div class="features">
                    <div class="vehicle_criteria_section">
                        <div class="subtitle">Search Vehicle by Criteria</div>   
                        
                        <form name="criteria_form" action="default_search_page.php" method="POST">
                            <table>
                                <!-- Dropdown -->
                                <tr>
                                    <td class="item_label">Vehicle type</td>
                                    <td class="item_label">Manufacturer</td>
                                    <td class="item_label">Year</td>
                                    <td class="item_label">Fuel type</td>
                                    <td class="item_label">Color</td>                                    
                                </tr>
                                <!-- Dropdown -->
                                <tr>
                                    <td>
                                        <?php
                                            $query = "SELECT vehicle_type FROM Vehicle_Type";
                                            $result = mysqli_query($db, $query);
                                            $vehicle_type_db = array();
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $vehicle_type_db[] = $row['vehicle_type'];
                                            }
                                        ?>
                                        <select name="vehicle_type">
                                            <option value="">Select a Vehicle Type</option>
                                            <?php foreach ($vehicle_type_db as $vehicle_type) { ?>
                                                <option value="<?php echo $vehicle_type; ?>"><?php echo $vehicle_type; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php
                                            $query = "SELECT manufacturer_name FROM Manufacturer_Name";
                                            $result = mysqli_query($db, $query);
                                            $manufacturer_name_db = array();
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $manufacturer_name_db[] = $row['manufacturer_name'];
                                            }
                                        ?>
                                        <select name="manufacturer_name">
                                            <option value="">Select a Manufacturer</option>
                                            <?php foreach ($manufacturer_name_db as $manufacturer_name) { ?>
                                                <option value="<?php echo $manufacturer_name; ?>"><?php echo $manufacturer_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php
                                        $currentYear = date('Y'); // Get the current year 
                                        $startYear = 1980; // Define a range of years, e.g., from 1980 to the current year
                                        $years = array();
                                        for ($i = $currentYear; $i >= $startYear; $i--) {
                                            $years[] = $i;
                                        } 
                                        echo '<select name="model_year">';
                                        echo '<option value="">Select a Year</option>';
                                        foreach ($years as $year) {
                                            echo '<option value="' . $year . '">' . $year . '</option>';
                                        }
                                        echo '</select>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            $query = "SELECT DISTINCT fuel_type FROM Vehicle";
                                            $result = mysqli_query($db, $query);
                                            $fuel_type_db = array();
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $fuel_type_db[] = $row['fuel_type'];
                                            }
                                        ?>
                                        <select name="fuel_type">
                                            <option value="">Select a Fuel type</option>
                                            <?php foreach ($fuel_type_db as $fuel_type) { ?>
                                                <option value="<?php echo $fuel_type; ?>"><?php echo $fuel_type; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php
                                            $query = "SELECT color FROM Colors";
                                            $result = mysqli_query($db, $query);
                                            $color_db = array();
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $color_db[] = $row['color'];
                                            }
                                        ?>
                                        <select name="color">
                                            <option value="">Select a Color</option>
                                            <?php foreach ($color_db as $color) { ?>
                                                <option value="<?php echo $color; ?>"><?php echo $color; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <!-- Key in -->
                                <tr>
                                    <td class="item_label">Keyword</td>
                                </tr>
                                <tr>
                                    <td><input type="text" name="keyword" /></td>
                                </tr>
                            </table>
                            <a href="javascript:criteria_form.submit();" class="fancy_button">Search</a>
                        </form>
                    </div>

                    <div class='Vehicle_num'>
                        <!-- <div class='subtitle'>Vehicle Number</div> -->
                        <?php require('view_vehicle_number.php');?>
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
                                if ($_SESSION['user_permission_index']>2){
                                    require('filter_vehicle_by_sold_status.php');
                                } else {
                                    // if (!is_null($veh_result) && mysqli_num_rows($veh_result) > 0) {
                                    //     while ($row = mysqli_fetch_array($veh_result, MYSQLI_ASSOC)){
                                    //         $VIN = urlencode($row['VIN']);
                                    //         print "<tr>";
                                    //         print "<td><a href='view_select_vehicle.php?VIN=$VIN'>{$row['VIN']}</a></td>";
                                    //         print "<td>{$row['vehicle_type']}</td>";
                                    //         print "<td>{$row['manufacturer_name']}</td>";
                                    //         print "<td>{$row['model_name']}</td>";
                                    //         print "<td>{$row['model_year']}</td>";
                                    //         print "<td>{$row['fuel_type']}</td>";
                                    //         print "<td>{$row['vehicle_colors']}</td>";
                                    //         print "<td>{$row['horsepower']}</td>";		
                                    //         print "<td>{$row['sale_price']}</td>";						
                                    //         print "</tr>";
                                    //     }
                                    // } else {
                                    //     echo "<tr><td colspan='9'>Sorry, it looks like we don’t have that in stock!</td></tr>";
                                    // }
                                    $vehicle_list = $_SESSION['List_Vehicle'];
                                    if (count($vehicle_list) > 0) { 
                                        foreach ($vehicle_list as $vehicle){
                                            $VIN = urlencode($vehicle['VIN']);
                                            print "<tr>";
                                            print "<td><a href='view_select_vehicle.php?VIN=$VIN'>{$vehicle['VIN']}</a></td>";
                                            print "<td>{$vehicle['vehicle_type']}</td>";
                                            print "<td>{$vehicle['manufacturer_name']}</td>";
                                            print "<td>{$vehicle['model_name']}</td>";
                                            print "<td>{$vehicle['model_year']}</td>";
                                            print "<td>{$vehicle['fuel_type']}</td>";
                                            print "<td>{$vehicle['vehicle_colors']}</td>";
                                            print "<td>{$vehicle['horsepower']}</td>";		
                                            print "<td>{$vehicle['sale_price']}</td>";	
                                            // print "<td>{$row['buy']}</td>";						
                                            print "</tr>";
                                            // echo $row['vehicle_type'];
                                        }
                                    } else {
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
</body>
</html>