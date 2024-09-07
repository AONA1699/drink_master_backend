<?php
$secretKey = "cc3b7a35-0020-4d33-9443-e9c60371b7e0";

// Función para codificar un JWT
function base64UrlEncode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

// Función para generar un JWT manualmente
function generateJWT($userId) {
    global $secretKey;
    
    $header = base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64UrlEncode(json_encode(['user_id' => $userId, 'exp' => time() + 3600]));
    
    $signature = hash_hmac('sha256', "$header.$payload", $secretKey, true);
    $signature = base64UrlEncode($signature);
    
    return "$header.$payload.$signature";
}

// Función para decodificar un JWT manualmente
function decodeJWT($jwt) {
    global $secretKey;
    
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return null; // Token inválido
    }

    list($header, $payload, $signature) = $parts;
    
    $validSignature = hash_hmac('sha256', "$header.$payload", $secretKey, true);
    $validSignature = base64UrlEncode($validSignature);
    
    if ($signature === $validSignature) {
        $payload = json_decode(base64_decode($payload), true);
        if ($payload['exp'] >= time()) {
            return $payload;
        }
    }
    
    return null; // Token inválido o expirado
}
?>
