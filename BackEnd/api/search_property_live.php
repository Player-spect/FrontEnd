<?php
ini_set('display_errors', 0);
require_once '../config/database.php';
header('Content-Type: application/json');

$provincia = trim($_GET['provincia'] ?? '');
$comuna = trim($_GET['comuna'] ?? '');
$sector = trim($_GET['sector'] ?? '');
$searchText = trim($provincia . ' ' . $comuna . ' ' . $sector);

if ($searchText === '') {
    echo json_encode(['success' => false, 'results' => []]);
    exit;
}

$searchTextNormalized = '%' . strtolower($searchText) . '%';

try {
    $conn = getConnection();
    $stmt = $conn->prepare(" 
        SELECT id, tipo, descripcion, precio_clp, provincia, comuna, sector 
        FROM propiedades
        WHERE LOWER(COALESCE(tipo, '')) LIKE :searchText
           OR LOWER(COALESCE(descripcion, '')) LIKE :searchText
           OR LOWER(COALESCE(provincia, '')) LIKE :searchText
           OR LOWER(COALESCE(comuna, '')) LIKE :searchText
           OR LOWER(COALESCE(sector, '')) LIKE :searchText
        LIMIT 20
    ");
    $stmt->execute(['searchText' => $searchTextNormalized]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'results' => $results]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en búsqueda en tiempo real']);
}
