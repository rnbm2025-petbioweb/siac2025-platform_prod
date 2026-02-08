<?php
// /var/www/siac2025.com/includes/db.php

$DB_HOST = 'mysql_petbio_secure';   // nombre del servicio
$DB_NAME = 'db__produccion_petbio_segura_2025';
$DB_USER = 'root';                  // o el usuario real si existe
$DB_PASS = getenv('R00t_Segura_2025!');

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Error de conexión a la base de datos'
    ]);
    exit;
}
