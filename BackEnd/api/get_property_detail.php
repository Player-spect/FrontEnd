<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once "../config/database.php";
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID de propiedad no proporcionado']);
    exit;
}

try {
    $conn = getConnection();

    $stmt = $conn->prepare("
        SELECT p.*,
               (SELECT ruta FROM fotos_propiedad WHERE propiedad_id = p.id AND es_principal = 1 LIMIT 1) as main_image
        FROM propiedades p
        WHERE p.id = :id
    ");

    $stmt->execute(['id' => $id]);
    $propiedad = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($propiedad) {
        echo json_encode(['success' => true, 'data' => $propiedad]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Propiedad no encontrada en el sistema']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al consultar la base de datos']);
}
