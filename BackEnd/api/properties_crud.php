<?php
ini_set('display_errors', 0);
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
            $stmt = $conn->prepare("
                    SELECT p.id, p.tipo, p.descripcion, p.dormitorios, p.banos, p.area_construida,
                    p.area_terreno, p.precio_clp, p.precio_uf, p.fecha_publicacion, p.solicitar_visita, p.bodega, p.estacionamiento,
                    p.logia, p.cocina_amoblada, p.antejardin, p.patio_trasero, p.piscina, p.provincia, p.comuna, p.sector, p.usuario_id,
                    p.created_at AS Propiedad, (select ruta FROM fotos_propiedad WHERE propiedad_id = p.id AND es_principal = 1 LIMIT 1) as main_image
                    FROM propiedades p
                    WHERE p.usuario_id = :user_id
                    ORDER BY p.created_at DESC
            ");

            $stmt->execute(['user_id' => $_SESSION['user_id']]);

            $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $properties]);
            break;


        case 'create':
            // Creacion de Propiedad
            $stmt = $conn->prepare("
                INSERT INTO propiedades (
                    tipo, descripcion, dormitorios, banos, area_construida, area_terreno,
                    precio_clp, precio_uf, fecha_publicacion, solicitar_visita,
                    bodega, estacionamiento, logia, cocina_amoblada, antejardin, patio_trasero, piscina,
                    provincia, comuna, sector, usuario_id
                ) VALUES (
                    :tipo, :desc, :dorm, :banos, :area_c, :area_t,
                    :precio_clp, :precio_uf, :fecha, :visita,
                    :bodega, :estac, :logia, :cocina, :ante, :patio, :piscina,
                    :prov, :com, :sec, :user_id
                )");

            $stmt->execute([
                'tipo' => $_POST['tipo'],
                'desc' => $_POST['descripcion'] ?? 'Sin Descripcion',
                'dorm' => $_POST['dormitorios'],
                'banos' => $_POST['banos'],
                'area_c' => $_POST['area-construida'],
                'area_t' => $_POST['area-terreno'],
                'precio_clp' => $_POST['precio-clp'],
                'precio_uf' => $_POST['precio-uf'],
                'fecha' => date('Y-m-d'),
                'visita' => isset($_POST['solicitar-visita']) ? 1 : 0,
                'bodega' => isset($_POST['bodega']) ? 1 : 0,
                'estac' => isset($_POST['estacionamiento']) ? 1 : 0,
                'logia' => isset($_POST['logia']) ? 1 : 0,
                'cocina' => isset($_POST['cocina-amoblada']) ? 1 : 0,
                'ante' => isset($_POST['antejardin']) ? 1 : 0,
                'patio' => isset($_POST['patio-trasero']) ? 1 : 0,
                'piscina' => isset($_POST['piscina']) ? 1 : 0,
                'prov' => $_POST['provincia'],
                'com' => $_POST['comuna'],
                'sec' => $_POST['sector'],
                'user_id' => $_SESSION['user_id']
            ]);

            $prop_id = $conn->lastInsertId();

            if(isset($_FILES['fotos'])){
                $root_dir = dirname(__DIR__,2);
                $upload_dir = $root_dir . '/uploads/propiedades/';

                foreach($_FILES['fotos']['tmp_name'] as $key => $tmp_name){
                    if($_FILES['fotos']['error'][$key] === UPLOAD_ERR_OK){
                        $filename = uniqid('prop_' . $prop_id . '_') . '.jpg';
                        move_uploaded_file($tmp_name, $upload_dir . $filename);

                        $stmt_img = $conn->prepare("
                                INSERT INTO fotos_propiedad(propiedad_id, ruta, es_principal, orden)
                                VALUES(:prop_id, :ruta, :principal, :orden)
                        ");

                        $stmt_img->execute([
                           'prop_id' => $prop_id,
                           'ruta' => $filename,
                           'principal' => $key === 0 ? 1 : 0,
                           'orden' => $key
                        ]);
                    }
                }
            }
            echo json_encode(['success' => true, 'message' => 'Propiedad creada exitosamente']);
            break;

        case 'update':
            // Actualizacion de Propiedad
            $id = $_POST['id'];
            $stmt = $conn->prepare("
                UPDATE propiedades SET
                    tipo = :tipo,
                    descripcion = :desc,
                    dormitorios = :dorm,
                    banos = :banos,
                    area_construida = :area_c,
                    area_terreno = :area_t,
                    precio_clp = :precio_clp,
                    precio_uf = :precio_uf,
                    provincia = :provincia,
                    comuna = :comuna,
                    sector = :sector
                WHERE id = :id AND usuario_id = :user_id
            ");

            $stmt->execute([
                    'tipo' => $_POST['tipo'],
                    'desc' => $_POST['descripcion'],
                    'dorm' => $_POST['dormitorios'],
                    'banos' => $_POST['banos'],
                    'area_c' => $_POST['area-construida'],
                    'area_t' => $_POST['area-terreno'],
                    'precio_clp' => $_POST['precio-clp'],
                    'precio_uf' => $_POST['precio-uf'],
                    'provincia' => $_POST['provincia'],
                    'comuna' => $_POST['comuna'],
                    'sector' => $_POST['sector'],
                    'id' => $id,
                    'user_id' => $_SESSION['user_id']
                ]);
            echo json_encode(['success' => true, 'message' => 'Propiedad actualizada exitosamente']);
            break;

        case 'delete':
            // Eliminacion de Propiedad
            $id = $_POST['id'] ?? $_GET['id'];
            $root_dir = dirname(__DIR__, 2);
            $photo_dir = $root_dir . '/uploads/propiedades/';

            // Delete Photos
            $stmt_dir = $conn->prepare("SELECT ruta FROM fotos_propiedad WHERE propiedad_id = :id");
            $stmt_dir->execute(['id' => $id]);
            $fotos = $stmt_dir->fetchAll(PDO::FETCH_ASSOC);

            foreach($fotos as $foto){
                $file_path = $photo_dir . $foto['ruta'];
                if(file_exists($file_path)){
                    unlink($file_path);
                }
            }

            // Delete properties
            $stmt = $conn->prepare("DELETE FROM propiedades WHERE id = :id AND usuario_id = :user_id");
            $stmt->execute([
                'id' => $id,
                'user_id' => $_SESSION['user_id']
            ]);
            echo json_encode(['success' => true, 'message' => 'Propiedad eliminada exitosamente']);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Accion no valida']);
            http_response_code(400);
    }

}catch(PDOException $e){
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
    exit();
}





