<?php
ini_set('display_errors', 0);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol'])){
	header('Location: ../../FrontEnd/login.php');
	exit;
	}
