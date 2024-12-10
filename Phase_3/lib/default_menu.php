<?php
require('background.php');
?>
<div class="nav_bar">
	<ul>    
		<li><a href="login.php" <?php if($current_filename=='login.php') echo "class='active'"; ?>>User Login</a></li>                       
		<li><a href="default_search_page.php" <?php if($current_filename=='default_search_page.php') echo "class='active'"; ?>>Search Vehicle</a></li>              
	</ul>
</div>