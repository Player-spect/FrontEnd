<?php
ini_set('display_errors', 0);
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');


// Vallida y verifica que la solicitud solo sea por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
	http_response_code(405);
	echo json_encode(["success" => false, "message" => "Método no permitido"]);
	exit;
}

$mail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

// Valida que los campos no estén vacíos
if (empty($mail) || empty($password)){
	http_response_code(400);
	echo json_encode(["success" => false, "message" => "Correo electrónico y contraseña son requeridos."]);
	exit;
}


// Logica de Inicio de session
try {
	$conn = getConnection();
	$query = $conn->prepare(
		"SELECT id, email, password_hash, rol, nombre FROM usuarios
		WHERE email = :email and estado = 'activo' ");

	$query->execute(['email' => $mail]);
	$user = $query->fetch(PDO::FETCH_ASSOC);

	// Validacion de la existencia del usuario en la db
	if (!$user) {
		echo json_encode(['success' => false, 'message' => 'Credenciales Incorrectas']);
		exit;
	}

	// Validacion de la contraseña
	if (!password_verify($password, $user['password_hash'])){
		http_response_code(401);
		echo json_encode(['success' => false, 'message' => 'Credenciales Incorrectas']);
		exit;

	}
	$_SESSION['user_id'] = $user['id'];
	$_SESSION['user_name'] = $user['nombre'];
	$_SESSION['user_email'] = $user['email'];
	$_SESSION['user_rol'] = $user['rol'];

	echo json_encode([
		'success' => true,
		'message' => 'Inicio Exitoso',
		'redirect' => '../../FrontEnd/dashboard/dashboard-' . $user['rol'] . '.php'
	]);
} catch(PDOException $e){
	echo json_encode(['success' => false, 'message' => 'Error interno del Servidor']);
}
