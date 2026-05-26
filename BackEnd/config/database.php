<?php
ini_set('display_errors', 0);
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'admin');
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
?>
