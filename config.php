<?php
// Configuración de conexión a la base de datos
$host = 'localhost'; // Cambia esto por la dirección de tu servidor de base de datos
$db = 'licoreria_db'; // Cambia por el nombre de tu base de datos
$user = 'root'; // Cambia por tu usuario de base de datos
$pass = ''; // Cambia por tu contraseña de base de datos

try {
    // Crear la conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Configurar PDO para lanzar excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si falla la conexión, se muestra un mensaje de error
    die("Error en la conexión: " . $e->getMessage());
}
?>