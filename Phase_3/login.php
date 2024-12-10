<?php
include('lib/common.php');
// written by GTusername1

if($showQueries){
  array_push($query_msg, "showQueries currently turned ON, to disable change to 'false' in lib/common.php");
}

//Note: known issue with _POST always empty using PHPStorm built-in web server: Use *AMP server instead
if( $_SERVER['REQUEST_METHOD'] == 'POST') {

	$enteredUsername = mysqli_real_escape_string($db, $_POST['username']);
	$enteredPassword = mysqli_real_escape_string($db, $_POST['password']);

    if (empty($enteredUsername)) {
            array_push($error_msg,  "Please enter a username.");
    }

	if (empty($enteredPassword)) {
			array_push($error_msg,  "Please enter a password.");
	}
	
    if ( !empty($enteredUsername) && !empty($enteredPassword) )   { 

        $query = "SELECT password FROM `Logged_in_User` WHERE username='$enteredUsername'";
        
        $result = mysqli_query($db, $query);
        include('lib/show_queries.php');
        $count = mysqli_num_rows($result); 
        
        if (!empty($result) && ($count > 0) ) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $storedPassword = $row['password']; 
            
            $options = [
                'cost' => 8,
            ];
             //convert the plaintext passwords to their respective hashses
             // 'michael123' = $2y$08$kr5P80A7RyA0FDPUa8cB2eaf0EqbUay0nYspuajgHRRXM9SgzNgZO
            $storedHash = password_hash($storedPassword, PASSWORD_DEFAULT , $options);   //may not want this if $storedPassword are stored as hashes (don't rehash a hash)
            $enteredHash = password_hash($enteredPassword, PASSWORD_DEFAULT , $options); 
            
            if($showQueries){
                array_push($query_msg, "Plaintext entered password: ". $enteredPassword);
                //Note: because of salt, the entered and stored password hashes will appear different each time
                array_push($query_msg, "Entered Hash:". $enteredHash);
                array_push($query_msg, "Stored Hash:  ". $storedHash . NEWLINE);  //note: change to storedHash if tables store the plaintext password value
                //unsafe, but left as a learning tool uncomment if you want to log passwords with hash values
                //error_log('email: '. $enteredEmail  . ' password: '. $enteredPassword . ' hash:'. $enteredHash);
            }
            
            //depends on if you are storing the hash $storedHash or plaintext $storedPassword 
            if (password_verify($enteredPassword, $storedHash) ) {
                array_push($query_msg, "Password is Valid! ");
                $_SESSION['username'] = $enteredUsername;
                // check which group the user belongs to 
                
                // $query = "
                //     SELECT CASE
                //         WHEN '$enteredUsername' IN (SELECT username FROM Manager) 
                //             AND '$enteredUsername' IN (SELECT username FROM Inventory_Clerk)
                //             AND '$enteredUsername' IN (SELECT username FROM Salespeople) THEN 4 -- Owner
                //         WHEN '$enteredUsername' IN (SELECT username FROM Inventory_Clerk) THEN 1 -- Clerk
                //         WHEN '$enteredUsername' IN (SELECT username FROM Salespeople) THEN 2 -- Salesperson
                //         WHEN '$enteredUsername' IN (SELECT username FROM Manager) THEN 3 -- Manager
                //         ELSE 0 --Public User
                //     END AS user_permission_index";
                $query = "SELECT CASE
                            WHEN EXISTS (SELECT 1 FROM Manager WHERE username = '$enteredUsername') 
                                AND EXISTS (SELECT 1 FROM Inventory_Clerk WHERE username = '$enteredUsername') 
                                AND EXISTS (SELECT 1 FROM Salespeople WHERE username = '$enteredUsername') THEN 4 -- Owner
                            WHEN EXISTS (SELECT 1 FROM Inventory_Clerk WHERE username = '$enteredUsername') THEN 1 -- Clerk
                            WHEN EXISTS (SELECT 1 FROM Salespeople WHERE username = '$enteredUsername') THEN 2 -- Salesperson
                            WHEN EXISTS (SELECT 1 FROM Manager WHERE username = '$enteredUsername') THEN 3 -- Manager
                            ELSE 0 -- Public User
                        END AS user_permission_index;";
                
                $result = mysqli_query($db, $query);

                if ($result) {
                    $row = mysqli_fetch_array($result);
                    $user_permission_index = $row['user_permission_index'];
                
                    $_SESSION['user_permission_index'] = $row['user_permission_index'];
                    
                    if ($showQueries) {
                        array_push($query_msg, "User permission index: ". $user_permission_index);
                    } // Close this `if` block properly
                } else {
                    array_push($error_msg, "Error retrieving user permission: " . mysqli_error($db)); 
                }
                
                array_push($query_msg, "logging in... ");
                header(REFRESH_TIME . 'url=default_search_page.php');		//to view the password hashes and login success/failure
                
            } else {
                array_push($error_msg, "Login failed: " . $enteredUsername . NEWLINE);
                array_push($error_msg, "To demo enter: ". NEWLINE . "userxx". NEWLINE ."userpass");
            }
            
        } else {
                array_push($error_msg, "The username entered does not exist: " . $enteredUsername);
            }
    }
}
?>
<?php 
// Update total parts cost
$query = 
"UPDATE Parts_Order po
JOIN (
    SELECT p.order_number, SUM(p.unit_price * p.quantity) AS total_cost
    FROM Part p
    GROUP BY p.order_number
) AS p_total_cost ON po.order_number = p_total_cost.order_number
SET po.total_cost = p_total_cost.total_cost
";
$result = mysqli_query($db, $query);

// Update sale price
$query = 
"UPDATE Vehicle
SET Vehicle.sale_price = 1.25*Vehicle.purchase_price + 1.10*(
SELECT IFNULL (SUM(Parts_Order.total_cost), 0)
FROM Parts_Order
WHERE Parts_Order.VIN = Vehicle.VIN
)
";
mysqli_query($db, $query);
?>

<?php include("lib/header.php"); ?>
<title>North Avenue Login</title>
</head>
<body>
    <div id="main_container">
        <?php
        require('lib/background.php');
        ?>

        <div class="center_content">
            <div class="text_box">

                <form action="login.php" method="post" enctype="multipart/form-data">
                    <div class="title">North Avenue Login</div>
                    <div class="login_form_row">
                        <label class="login_label">Username:</label>
                        <input type="text" name="username" value="user01" class="login_input"/>
                    </div>
                    <div class="login_form_row">
                        <label class="login_label">Password:</label>
                        <input type="password" name="password" value="pass01" class="login_input"/>
                    </div>
                    <input type="image" src="img/login.gif" class="login"/>
                </form>
                </div>

                <?php include("lib/error.php"); ?>

                <div class="clear"></div>
            </div>
   
            <!-- 
			<div class="map">
			<iframe style="position:relative;z-index:999;" width="820" height="600" src="https://maps.google.com/maps?q=801 Atlantic Drive, Atlanta - 30332&t=&z=14&ie=UTF8&iwloc=B&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"><a class="google-map-code" href="http://www.embedgooglemap.net" id="get-map-data">801 Atlantic Drive, Atlanta - 30332</a><style>#gmap_canvas img{max-width:none!important;background:none!important}</style></iframe>
			</div>
             -->
					<?php include("lib/footer.php"); ?>

        </div>
    </body>
</html>