<?php
header('Content-Type: application/json');

require __DIR__ . '/includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['codigo']) || !preg_match('/^\d{6}$/', $data['codigo'])) {
    echo json_encode([
        'ok' => false,
        'error' => 'Código inválido'
    ]);
    exit;
}

$codigo = $data['codigo'];

$stmt = $pdo->prepare("
    SELECT nombre, estado
    FROM mascotas
    WHERE codigo_distintivo = :codigo
    LIMIT 1
");
$stmt->execute(['codigo' => $codigo]);

$mascota = $stmt->fetch();

if (!$mascota) {
    echo json_encode([
        'ok' => false,
        'error' => 'Mascota no encontrada'
    ]);
    exit;
}

echo json_encode([
    'ok' => true,
    'mascota' => $mascota
]);
