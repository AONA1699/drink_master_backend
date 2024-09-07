<?php
require 'config.php';
require 'jwt.php'; 

function registerUser($username, $email, $password, $rol_usuario) {
    global $pdo;

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $userId = bin2hex(random_bytes(16)); // Genera un user_id único

    $query = "INSERT INTO users (username, email, password, user_id, rol_usuario) VALUES (:username, :email, :password, :user_id, :rol_usuario)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':rol_usuario', $rol_usuario);

    if ($stmt->execute()) {
        return ['message' => 'User registered successfully'];
    } else {
        return ['message' => 'Registration failed'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $response = registerUser($data['username'], $data['email'], $data['password'], $data['rol_usuario']);
    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode($response);
}
?>
