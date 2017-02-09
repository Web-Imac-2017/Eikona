<?php

require_once '../models/class.user.php';

session_start();

if(!isset($_SESSION['user'])){
	header("LOCATION: login.php");	
}

require '../views/index.php';

?>