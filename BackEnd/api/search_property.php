<?php
ini_set('display_errors', 0);
require_once '../config/database.php';
header('Content-Type: application/json');

$provincia = $_GET['provincia'] ?? '';
$comuna = $_GET['comuna'] ?? '';
$sector = $_GET['sector'] ?? '';

if (empty($provincia) && empty($comuna) && empty($sector)) {
    echo json_encode(['success' => false, 'results' => []]);
    exit;
}
try {
    $conn = getConnection();
    $stmt = $conn->prepare("
        SELECT id, tipo, descripcion, precio_clp, provincia, comuna, sector 
        FROM propiedades
        WHERE provincia LIKE :provincia OR comuna LIKE :comuna OR sector LIKE :sector
        LIMIT 20
    ");
    $stmt->execute([
        'provincia' => '%' . $provincia . '%',
        'comuna' => '%' . $comuna . '%',
        'sector' => '%' . $sector . '%'
    ]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'results' => $results]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en búsqueda']);
}
