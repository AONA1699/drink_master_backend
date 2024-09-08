<?php
require 'config.php';
require 'headers.php';  // Archivo donde definimos los encabezados CORS y otros
require 'jwt.php';  // Incluye la lógica para manejar el JWT

// Función para validar el Bearer token y devolver el usuario_id
function validateToken($token) {
    // Decodificar el token (utiliza tu lógica de JWT o una librería)
    $decoded = decodeJWT($token);

    // Asegurarse de que el token es válido y contiene el user_id
    if ($decoded !== null && isset($decoded['user_id'])) {
        return $decoded['user_id'];  // Retorna el user_id si es válido
    }

    return false;  // Si no es válido, retorna false
}

// Función para guardar el pedido en la tabla 'caja'
function saveOrder($pedidos, $totalPedido, $usuario_id) {
    global $pdo;

    $pedidosJson = json_encode($pedidos); 

    $query = "INSERT INTO caja (pedidos, total_pedido, usuario_id) VALUES (:pedidos, :total_pedido, :usuario_id)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':pedidos', $pedidosJson);  // Convertir el array a JSON
    $stmt->bindParam(':total_pedido', $totalPedido);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();

    return $pdo->lastInsertId();
}

// Verificar que la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el Bearer token desde los encabezados de la solicitud
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        // Si el token no está presente, devolver 401 Unauthorized
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['message' => 'Unauthorized']);
        exit();
    }

    // Asignar el valor del encabezado 'Authorization' a una variable antes de usar str_replace
    $token = str_replace('Bearer ', '', $headers['Authorization']);

    // Validar el token y obtener el usuario_id desde el token

    $decoded = decodeJWT($token);
    if (!$decoded) {
        return ['message' => 'Invalid token'];
    }

    $usuario_id =  $decoded['user_id'];
    if (!$usuario_id) {
        // Si el token no es válido o no tiene user_id, devolver 401 Unauthorized
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['message' => 'Invalid token']);
        exit();
    }

    // Obtener los datos del pedido (carrito y total) desde el cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);
    $carrito = $data['carrito'] ?? [];
    $totalPedido = $data['total_pedido'] ?? 0;

    if (empty($carrito) || $totalPedido <= 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid order data']);
        exit();
    }

    // Crear el array de pedidos para almacenarlo en la columna 'pedidos'
    $pedidos = [];
    foreach ($carrito as $item) {
        $pedidos[] = [
            'nombre' => $item['nombre_producto'],
            'cantidad' => $item['quantity'],
            'valor' => $item['precio_producto'] * $item['quantity']
        ];
    }

    // Guardar el pedido en la tabla 'caja'
    $orderId = saveOrder($pedidos, $totalPedido, $usuario_id);

    // Responder con éxito
    header('Content-Type: application/json');  // Asegurar que se envía como JSON
    http_response_code(201);
    echo json_encode(['message' => 'Order placed successfully', 'order_id_' => $orderId]);
}
