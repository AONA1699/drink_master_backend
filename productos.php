<?php
require 'config.php';
require 'jwt.php';  // Aquí puedes agregar las funciones para generar y validar el JWT manualmente o con una librería

function getProductosConDetalles() {
    global $pdo;

    // Consulta SQL para obtener los detalles del producto
    $query = "
        SELECT 
            p.id AS producto_id,
            p.nombre AS nombre_producto,
            p.precio AS precio_producto,
            i.ruta_img AS ruta_imagen,
            s.cantidad_producto AS stock_producto
        FROM productos p
        LEFT JOIN imagenes i ON p.id = i.id_product
        LEFT JOIN stock s ON p.id = s.id_produc
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para validar el token JWT (puedes ajustarla dependiendo de cómo generes y valides el token)
function validateToken($token) {
    // Lógica para decodificar y validar el token (utiliza funciones manuales o una librería)
    $decoded = decodeJWT($token);
    return $decoded !== null;  // Si el token es válido, retorna true; de lo contrario, false
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener el token del encabezado Authorization
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

    // Si el token es válido, retornar la lista de productos
    $response = getProductosConDetalles();
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
