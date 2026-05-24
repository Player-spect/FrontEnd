<?php
session_start();
require_once "../config/database.php";
header('Content-Type: application/json');

// Validate Session

if(!isset($_SESSION['user_id'])){
    echo json_encode(['success' => false, 'message' => 'Not logging in']);
    http_response_code(401);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {

    $conn = getConnection();

    switch ($action){
        case 'list':
            // List the properties
            $stmt = $conn->query("
                    SELECT p.id, p.tipo, p.descripcion, p.dormitorios, p.banos, p.area_construida,
                    p.area_terreno, p.precio_clp, p.precio_uf, p.fecha_publicacion, p.solicitar_visita, p.bodega, p.estacionamiento,
                    p.logia, p.cocina_amoblada, p.antejardin, p.patio_trasero, p.piscina, p.provincia, p.comuna, p.sector, p.usuario_id,
                    p. created_at AS Propiedad, (select ruta FROM fotos_propiedad WHERE propiedad_id = p.id AND es_principal = 1 LIMIT 1) as main_image
                    FROM propiedades p
                    ORDER BY created_at DESC");

            $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $properties]);
            break;


        case 'create':
            // Creacion de Propiedad
            $stmt = $conn->query("");
            break;


        case 'update':
            // Actualizacion de Propiedad
            $stmt = $conn->query("");
            break;


        case 'delete':
            // Eliminacion de Propiedad
            $stmt = $conn->query("");
            break;

    }

}catch(PDOException $e){
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
    exit();
}





