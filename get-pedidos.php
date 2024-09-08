<?php
require 'config.php';
require 'headers.php';  
require 'jwt.php'; 

// Función para obtener todos los pedidos y la información del usuario
function getAllOrders() {
    global $pdo;

    $query = "
        SELECT 
            c.id AS pedido_id, 
            c.pedidos, 
            c.total_pedido, 
            c.fecha,
            u.username
        FROM caja c
        INNER JOIN users u ON c.usuario_id = u.user_id
        ORDER BY c.id DESC;


    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Verificar que la solicitud es de tipo GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        // Si el token no está presente, devolver 401 Unauthorized
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['message' => 'Unauthorized']);
        exit();
    }
    // Obtener todos los pedidos
    $orders = getAllOrders();

    // Responder con la lista de pedidos y la información del usuario
    header('Content-Type: application/json');
    echo json_encode($orders);
}
