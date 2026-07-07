<?php
ini_set('display_errors', 0);
define('DB_HOST', 'localhost');
define('DB_USER', 'user_pnk');
define('DB_PASS', 'psw');
define('DB_NAME', 'pnk_inmobiliaria');

function getConnection(){
	try {
		return new PDO(
			'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ";charset=utf8mb4",
			DB_USER,
			DB_PASS,
			[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
		);
	} catch(PDOException $e) {
		die(json_encode(['error' => 'Error de Coneccion: ' . $e->getMessage()]));
	}
}
