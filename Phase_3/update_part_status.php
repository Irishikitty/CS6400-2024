<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_number'])) {
    $orderNumber = $_POST['order_number'];
    $partNumber = $_POST['ven_p_n'];
    $VehicleVIN = $_POST['VIN'];
    $status = $_POST['status'];

    // Use the new status to update the database or perform the required action
    $query = 
    "UPDATE Part
    SET Part.status = CASE
    WHEN Part.status = 'ordered' THEN 'received'
    WHEN Part.status = 'received' THEN 'installed'
    -- WHEN Part.status = 'installed' THEN 'ordered' -- For testing only, since installed should be the terminal status
    ELSE Part.status -- No change if the transition is invalid
    END
    WHERE Part.order_number = '$orderNumber'
    AND Part.vendor_part_number = '$partNumber'
    ";
    mysqli_query($db, $query);

    // Update sale price
    $query = 
    "UPDATE Vehicle
    SET Vehicle.sale_price = 1.25*Vehicle.purchase_price + 1.10*(
    SELECT IFNULL (SUM(Parts_Order.total_cost), 0)
    FROM Parts_Order
    WHERE Parts_Order.VIN = '$VehicleVIN' 
    )
    WHERE Vehicle.VIN = '$VehicleVIN' 
    ";
    mysqli_query($db, $query);

    // Sleep for 1 second (1000 milliseconds)
    // sleep(2);

    // Access parts order and parts
    $query = 
    "SELECT Part.order_number, Parts_Order.vendor_name,
            Part.vendor_part_number, Part.unit_price,
            Part.quantity, Part.description, Part.status
    FROM Part
    JOIN Parts_Order ON Part.order_number = Parts_Order.order_number
    WHERE Part.VIN = '$VehicleVIN'
    ORDER BY Part.order_number ASC
    ";
    $result = mysqli_query($db, $query);

    // Optionally, reload the page or handle the updated status dynamically
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
    exit(); // Ensure that no further code is executed after the header
}
?>