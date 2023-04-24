<?php
session_start(); 

//echo 'Site en maintenance...'

if(!isset($_SESSION['LOGGED_USER'])) {
	header("Location: login.php");
	exit();
} else {
	header("Location: form_pointage.php");
	exit();
} 
?>