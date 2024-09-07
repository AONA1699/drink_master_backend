<?php
require 'config.php';
require 'jwt.php'; 

function loginUser($usernameOrEmail, $password) {
    global $pdo;

    $query = "SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':usernameOrEmail', $usernameOrEmail);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $token = generateJWT($user['user_id']);
        return ['token' => $token];
    } else {
        return ['message' => 'Invalid credentials'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $response = loginUser($data['usernameOrEmail'], $data['password']);
    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode($response);
}
?>
