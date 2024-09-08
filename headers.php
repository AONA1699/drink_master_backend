<?php
// Encabezados CORS para permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Añadir el encabezado para saltar la advertencia de navegador de Ngrok
header("ngrok-skip-browser-warning: true");

// Si la solicitud es OPTIONS (preflight), se termina la ejecución aquí con un código 200
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
