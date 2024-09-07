<?php
require 'config.php';
require 'jwt.php'; 

function getUserInfo($token) {
    global $pdo;

    $decoded = decodeJWT($token);
    if (!$decoded) {
        return ['message' => 'Invalid token'];
    }

    $query = "SELECT * FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $decoded['user_id']);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        unset($user['password']); // No enviar el password en la respuesta
        return $user;
    } else {
        return ['message' => 'User not found'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $response = getUserInfo($token);
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($response);
    } else {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['message' => 'No token provided']);
    }
}
?>
