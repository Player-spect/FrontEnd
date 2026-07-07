<?php
ini_set('display_errors', 0);
require_once '../config/database.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'images' => [], 'message' => 'ID no proporcionado']);
    exit;
}

try {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT ruta FROM fotos_propiedad WHERE propiedad_id = :id ORDER BY orden ASC");
    $stmt->execute(['id' => $id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    echo json_encode(['success' => true, 'images' => $images]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'images' => [], 'message' => 'Error al cargar imágenes']);
}
