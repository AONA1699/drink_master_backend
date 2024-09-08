<?php
require 'config.php';
require 'jwt.php';

function actualizarStock($producto_id, $cantidad_a_agregar) {
    global $pdo;

    // Consultar el stock actual del producto
    $query = "SELECT cantidad_producto FROM stock WHERE id_produc = :producto_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
    $stmt->execute();

    $stock_actual = $stmt->fetchColumn();

    if ($stock_actual !== false) {
        // Sumar la cantidad ingresada al stock actual
        $nuevo_stock = $stock_actual + $cantidad_a_agregar;

        // Actualizar el stock en la base de datos
        $updateQuery = "UPDATE stock SET cantidad_producto = :nuevo_stock WHERE id_produc = :producto_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':nuevo_stock', $nuevo_stock, PDO::PARAM_INT);
        $updateStmt->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
        $updateStmt->execute();

        return ['message' => 'Stock actualizado correctamente', 'nuevo_stock' => $nuevo_stock];
    } else {
        return ['message' => 'Producto no encontrado'];
    }
}

function validateToken($token) {
    // Lógica para decodificar y validar el token (utiliza funciones manuales o una librería)
    $decoded = decodeJWT($token);
    return $decoded !== null;  // Si el token es válido, retorna true; de lo contrario, false
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        // Si el token no está presente, devolver 401 Unauthorized
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['message' => 'Unauthorized']);
        exit();
    }

    // Verificar el formato del token y eliminar el prefijo "Bearer "
    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    // Validar el token
    if (!validateToken($token)) {
        // Si el token no es válido, devolver 401 Unauthorized
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['message' => 'Invalid token']);
        exit();
    }

    // Obtener los datos enviados
    $data = json_decode(file_get_contents('php://input'), true);
    $producto_id = $data['producto_id'];
    $cantidad_a_agregar = $data['cantidad'];

    // Validar que ambos valores estén presentes
    if (isset($producto_id) && isset($cantidad_a_agregar)) {
        $response = actualizarStock($producto_id, $cantidad_a_agregar);
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['message' => 'Faltan datos']);
    }
}
