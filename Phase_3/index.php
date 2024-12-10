<?php

// written by GTusername1

session_start();
$_SESSION['user_permission_index'] = -1;
header("Location: default_search_page.php");
die();
/*
if (empty($_SESSION['username']) ){ 
    header("Location: default_serach_page.php");
}else{
    header("Location: user_login.php");
    die();
}
 */
?>