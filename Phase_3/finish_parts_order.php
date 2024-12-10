<?php
include('lib/common.php');

$order_number = $_SESSION['current_order_number'] ?? null;

if (!$order_number) {
    echo "Error: No active parts order to finalize.";
    header("Location: add_parts_order.php");
    exit();
} 

$query = "SELECT COUNT(*) AS cnt FROM Part WHERE order_number = '$order_number'";
$result = mysqli_query($db, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ((int)$row['cnt'] === 0) {
        header("Location: view_select_vehicle.php");
        echo "<p style='color: red;'>Add some parts to finalize the order.</p>";
        exit();
    }
} else {
    echo "<p style='color: red;'>Error checking parts: " . mysqli_error($db) . "</p>";
    exit();
}

$query = "
    UPDATE Parts_Order
    SET total_cost = (
        SELECT SUM(unit_price * quantity)
        FROM Part
        WHERE order_number = '$order_number'
    )
    WHERE order_number = '$order_number'
";

if (mysqli_query($db, $query)) {
    unset($_SESSION['current_order_number']);
    echo "<p style='color: green;'>Parts order finalized successfully!</p>";
    header("Location: view_select_vehicle.php");
    exit();
} else {
    echo "<p style='color: red;'>Error finalizing parts order: " . mysqli_error($db) . "</p>";
    exit();
}
?>
