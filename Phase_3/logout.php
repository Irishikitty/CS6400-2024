<?php

// written by GTusername1

/* destroy session data */
session_start();
session_destroy();
$_SESSION = array();

/* redirect to default_search page */
header('Location: index.php');

?>