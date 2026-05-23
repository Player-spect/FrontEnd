<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol'])){
	header('Location: ../../error-session.html');
	exit;
	}
?>
