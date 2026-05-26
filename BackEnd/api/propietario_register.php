<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once "../config/database.php";
header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

// Recibir los datos del formulario
$rut = $_POST['rut'] ?? '';
$nombre_completo = $_POST['nombre'] ?? '';
$fechaNacimiento = $_POST['fecha-nacimiento'] ?? '';
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$sexo = $_POST['sexo'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$num_propiedad = $_POST['propiedad'] ?? '';

// Validar que los campos críticos no lleguen vacíos
if(empty($email) || empty($password) || empty($num_propiedad)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']);
    exit();
}

// Encriptar contraseña
$password_hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $conn = getConnection();

    $query = $conn->prepare("
            INSERT INTO usuarios (rut, nombre, email, password_hash, rol, sexo, telefono, fecha_nacimiento, estado, certificado_path)
            VALUES (:rut, :nombre, :email, :password, 'propietario', :sexo, :tel, :fecha, 'activo', :num_propiedad)
        ");

    $query->execute([
        'rut' => $rut,
        'nombre' => $nombre_completo,
        'email' => $email,
        'password' => $password_hash,
        'sexo' => $sexo,
        'tel' => $telefono,
        'fecha' => $fechaNacimiento,
        'num_propiedad' => $num_propiedad //
    ]);

    echo json_encode(['success' => true, 'message' => 'Propietario registrado exitosamente']);

} catch(PDOException $e){
    if($e->getCode() == 23000){
        http_response_code(409);
        echo json_encode(['success'=>false, 'message' => "Error al registrar propietario"]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error interno del servidor de base de datos.']);
    }
}
