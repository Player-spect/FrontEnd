<?php
ini_set('display_errors', 0);
require_once "../config/database.php";
header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    http_response_code(401);
    exit();
}

// data of requests
$rut = $_POST['rut'] ?? '';
$nombre_completo = $_POST['nombre'] ?? '';
$fechaNacimiento = $_POST['fecha-nacimiento'] ?? '';
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$sexo = $_POST['sexo'] ?? '';
$telefono = $_POST['telefono'] ?? '';

// validate_rut($rut);

$password = password_hash($password, PASSWORD_BCRYPT);


// Validate pdf
$cert_path = null;
if(isset($_FILES['certificado']) && $_FILES['certificado']['error'] === UPLOAD_ERR_OK){
    $type = strtolower(pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION));
    if($type !== 'pdf'){
        echo json_encode(['success'=>false, 'message' => 'Formato de archivo invalido']);
        http_response_code(400); // verificar
        exit();
    }

    $file_name = uniqid('cert_') . '.pdf';

    // ✅ CORRECTO
    $root = dirname(__DIR__, 2);
    $upload_dir = $root . '/uploads/pdf/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    $file_path = $upload_dir . $file_name;
    // Guardar en BD la ruta relativa:
    $cert_path = 'uploads/pdf/' . $file_name;
}

// init connection and create user
try {
    $conn = getConnection();
    $query = $conn->prepare("
            INSERT INTO usuarios (rut, nombre, email, password_hash, rol, sexo, telefono, fecha_nacimiento, certificado_path, estado)
            VALUES (:rut, :nombre, :email, :password, 'gestor', :sexo, :tel, :fecha, :cert, 'activo')
        ");

    $query->execute([
        'rut' => $rut,
        'nombre' => $nombre_completo,
        'email' => $email,
        'password' => $password,
        'sexo' => $sexo,
        'tel' => $telefono,
        'fecha' => $fechaNacimiento,
        'cert' => $upload_dir,
    ]);

    echo json_encode(['success' => true, 'message' => 'Registro exitoso']);

} catch(PDOException $e){
    if($e->getCode() == 23000){
        echo json_encode(['success'=>false, 'message' => "No se puede registrar"]);
        http_response_code(409);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error interno del Servidor.' . $e->getMessage()]);
        http_response_code(500);

    }
}

