<?php
include('lib/common.php');
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
                        <div class="subtitle">View reports</div>   
                    </div>

                    <div class='profile_section'>
                        <a href="form_seller_history.php" target="_blank" style="font-size: 18px;">Seller History</a>
                        <br>
                        <a href="form_avg_time.php" target="_blank" style="font-size: 18px;">Average Time in Inventory</a>
                        <br>
                        <a href="form_price_per_condition.php" target="_blank" style="font-size: 18px;">Price Per Condition</a>
                        <br>
                        <a href="form_parts_stat.php" target="_blank" style="font-size: 18px;">Parts Statistics</a>
                        <br>
                        <a href="form_month_sale.php" target="_blank" style="font-size: 18px;">Monthly Sales</a>
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