<?php
require('background.php');
?>

<div class="nav_bar">
     <ul>    
     <li><a href="default_search_page.php" <?php if($current_filename=='default_search_page.php') echo "class='active'"; ?>>Search Vehicle</a></li>    
     <li><a href="default_search_page/search_vehicle_by_VIN.php" <?php if($current_filename=='default_search_page/search_vehicle_by_VIN.php') echo "class='active'"; ?>>Search Vehicle by VIN</a></li>     
     <li><a href="view_reports.php" <?php if($current_filename=='view_reports.php') echo "class='active'"; ?>>View Reports</a></li>    
     <li><a href="logout.php" <span class='glyphicon glyphicon-log-out'></span> Log Out</a></li>           
     </ul>
</div>