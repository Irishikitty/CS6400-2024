<form method="POST" action="default_search_page.php">
    <input type="submit" name="filter" value="all">
    <input type="submit" name="filter" value="sold">
    <input type="submit" name="filter" value="unsold">
</form>

<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $filtered_list = $_SESSION['List_Vehicle'];
        
        if (isset($_POST['filter'])){
            // print_r($_SESSION['List_Vehicle']);
            $filter = $_POST['filter'];
            // echo $filter;
            if ($filter === 'sold') {
                // soldlist
                $filtered_list = array_filter($_SESSION['List_Vehicle'], function($row) {
                    return $row['buy']!==null; // Filter based on a condition
                });
            } elseif ($filter === 'unsold') {
                // unsold list
                $filtered_list = array_filter($_SESSION['List_Vehicle'], function($row) {
                    return $row['buy']===null; // Filter based on a condition
                });
            } 
            else {
                $filtered_list = $_SESSION['List_Vehicle'];
                foreach($filtered_list as $veh):
                    // echo $veh['vehicle_type'];
                endforeach;
            }
        }
        if (count($filtered_list) > 0) { 
            foreach ($filtered_list as $vehicle){
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


