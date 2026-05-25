<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$query = $_GET['q'] ?? '';

if (empty($query)) {
    echo json_encode(['success' => false, 'results' => []]);
    exit;
}

try {
    $conn = getConnection();



    $stmt = $conn->prepare("
        SELECT id, tipo, descripcion, precio_clp, provincia, comuna, sector, (SELECT ruta FROM fotos_propiedad WHERE propiedad_id = propiedades.id AND es_principal = 1 LIMIT 1) as foto
        FROM propiedades
        WHERE provincia LIKE :q OR comuna LIKE :q OR sector LIKE :q OR tipo LIKE :q
        LIMIT 20
    ");

    $stmt->execute(['q' => '%' . $query . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'results' => $results]);

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en búsqueda']);
}
