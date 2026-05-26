<?php
require_once "../config/database.php";
require_once "../includes/session_checker.php";

header('Content-Type: application/json');

if ($_SESSION['user_rol'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Acceso denegado"]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;
$conn = getConnection();

try {
    switch ($action) {
        // --- LISTAR ---
        case 'list_usuarios':
            $stmt = $conn->query("SELECT id, nombre, email, rol, estado FROM usuarios");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            break;

        case 'list_propiedades':
            $stmt = $conn->query("SELECT id, tipo, sector, precio_clp FROM propiedades");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            break;

        // --- ACCIONES ---
        case 'delete_usuario':
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
            break;

        case 'toggle_estado':
            $stmt = $conn->prepare("UPDATE usuarios SET estado = IF(estado='activo', 'inactivo', 'activo') WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
            break;

        case 'delete_propiedad':
            $stmt = $conn->prepare("DELETE FROM propiedades WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Propiedad eliminada']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ']);
}

