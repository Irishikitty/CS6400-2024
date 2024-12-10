<?php
include('lib/common.php');
// written by GTusername4

    $query1 = " SELECT *
                FROM Vehicle
                WHERE NOT EXISTS (
                SELECT 1
                FROM Part P
                WHERE P.VIN = Vehicle.VIN
                AND P.status IN ('ordered', 'received')
                )
                AND NOT EXISTS ( 
                SELECT 1
                FROM Buy
                WHERE Buy.VIN = Vehicle.VIN
                )
                GROUP BY Vehicle.VIN;";

    $query2 = " SELECT *
                FROM Vehicle
                WHERE NOT EXISTS ( 
                SELECT 1
                FROM Buy
                WHERE Buy.VIN = Vehicle.VIN
                )
                GROUP BY Vehicle.VIN;";

    $query = $query1;
    $vehnum_result = mysqli_query($db, $query);
    $sail_avail = mysqli_num_rows($vehnum_result);
    // echo $sail_avail;
    include('lib/show_queries.php');
    if (mysqli_affected_rows($db) == -1) {
        array_push($error_msg,  "SELECT ERROR:Failed to find vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
    }

    // $_SESSION['user_permission_index'] = 3;
    if ($_SESSION['user_permission_index']>2 || $_SESSION['user_permission_index']==1) {// manager=3 or owner=4 or inventroy=1
        $query = $query2;
        $vehnum_result = mysqli_query($db, $query);
        $unsold_num = mysqli_num_rows($vehnum_result);
        // echo $unsold_num;
        // $count = mysqli_num_rows($vehnum_result);
        include('lib/show_queries.php');
        if (mysqli_affected_rows($db) == -1) {
            array_push($error_msg,  "SELECT ERROR:Failed to find vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
        }
        $pend_num = $unsold_num - $sail_avail;
        // echo $pend_num;
    }
?>
<table>
    <tr>
        <?php
            echo "<td class='heading'>Available Vehicle Number</td>";
            if ($_SESSION['user_permission_index'] > 2 || $_SESSION['user_permission_index'] == 1) {
                echo "<td class='heading'>Pending Vehicle Number</td>";
            }
        ?>
    </tr>
    <tr>
        <?php
            echo "<td>{$sail_avail}</td>";
            if ($_SESSION['user_permission_index'] > 2 || $_SESSION['user_permission_index'] == 1) {
                echo "<td>{$pend_num}</td>";
            }
        ?>
    </tr>
</table>

