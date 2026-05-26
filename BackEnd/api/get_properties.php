<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once "../config/database.php";
header('Content-Type: application/json');

try {
    $conn = getConnection();

    // Traemos las propiedades y su foto principal de forma pública
    $stmt = $conn->query("
        SELECT p.id, p.tipo, p.descripcion, p.dormitorios, p.banos, p.area_construida,
               p.precio_clp, p.precio_uf, p.fecha_publicacion, p.comuna, p.sector,
               (SELECT ruta FROM fotos_propiedad WHERE propiedad_id = p.id AND es_principal = 1 LIMIT 1) as main_image
        FROM propiedades p
        ORDER BY p.created_at DESC 
        LIMIT 10
    ");

    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $properties]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al obtener las propiedades.']);
}
