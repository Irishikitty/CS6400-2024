<?php
require('background.php');
?>

<div class="nav_bar">
    <?php
    echo "<ul>";
    if ($_SESSION['user_permission_index']==-1){
        echo "<li><a href='login.php' " . ($current_filename == 'login.php' ? "class='active'" : "") . ">User Login</a></li>";
    }
    echo "<li><a href='default_search_page.php' " . ($current_filename == 'default_search_page.php' ? "class='active'" : "") . ">Search Vehicle</a></li>";
    if ($_SESSION['user_permission_index']>0){
        echo "<li><a href='search_vehicle_by_VIN.php' " . ($current_filename == 'search_vehicle_by_VIN.php' ? "class='active'" : "") . ">Search Vehicle by VIN</a></li>";
    }
    if ($_SESSION['user_permission_index']==1 || $_SESSION['user_permission_index']==4){
        echo "<li><a href='add_vehicle.php' " . ($current_filename == 'add_vehicle.php' ? "class='active'" : "") . ">Add Vehicle</a></li>";
    }
    if ($_SESSION['user_permission_index']==3 || $_SESSION['user_permission_index']==4){
        echo "<li><a href='view_reports.php' " . ($current_filename == 'view_reports.php' ? "class='active'" : "") . ">View Reports</a></li>";
    }
    if ($_SESSION['user_permission_index']>-1){
        echo "<li><a href='logout.php'><span class='glyphicon glyphicon-log-out'></span> Log Out</a></li>";
    }
    echo "</ul>";
    ?>
</div>