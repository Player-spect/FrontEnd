<?php
ini_set('display_errors', 0);
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol'])){
	header('Location: ../../FrontEnd/login.html');
	exit;
	}
