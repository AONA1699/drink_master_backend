<?php
require 'config.php';
require 'jwt.php'; 

function loginUser($usernameOrEmail, $password) {
    global $pdo;

    // Consulta para verificar usuario por nombre de usuario o correo electrónico
    $query = "SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':usernameOrEmail', $usernameOrEmail);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y la contraseña es correcta
    if ($user && password_verify($password, $user['password'])) {
        // Generar un token JWT si las credenciales son correctas
        $token = generateJWT($user['user_id']);
        return ['token' => $token];
    } else {
        // Si las credenciales no son correctas
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos JSON del cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Intentar autenticar al usuario
    $response = loginUser($data['usernameOrEmail'], $data['password']);
    
    // Configurar el encabezado de respuesta como JSON
    header('Content-Type: application/json');
    
    if ($response) {
        // Si el login es exitoso, retornar código 200 con el token
        http_response_code(200);
        echo json_encode($response);
    } else {
        // Si las credenciales son inválidas, retornar código 401
        http_response_code(401);
        echo json_encode(['message' => 'Invalid credentials']);
    }
}
?>
