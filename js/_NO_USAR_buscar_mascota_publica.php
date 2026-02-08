<?php
header('Content-Type: application/json; charset=utf-8');

// 1️⃣ Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// 2️⃣ Validar código (6 dígitos)
$codigo = $_POST['codigo'] ?? '';
if (!preg_match('/^[0-9]{6}$/', $codigo)) {
    echo json_encode(['encontrada' => false]);
    exit;
}

// 3️⃣ Conexión BD (PDO)
//require_once dirname (__DIR__) . '/siac2025.com/includes/db.php';
require_once __DIR__ . '/includes/db.php';


try {

    /* =====================================================
       4️⃣ Buscar mascota por código público
    ===================================================== */
    $sqlMascota = "
        SELECT id, nombre, raza, ciudad
        FROM registro_mascotas
        WHERE RIGHT(numero_documento, 6) = ?
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sqlMascota);
    $stmt->execute([$codigo]);
    $mascota = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mascota) {
        echo json_encode(['encontrada' => false]);
        exit;
    }

    /* =====================================================
       5️⃣ Buscar extravío activo (SI EXISTE)
       👉 AQUÍ ESTABA EL PROBLEMA
    ===================================================== */
    $sqlExtravio = "
        SELECT id_extravio
        FROM registro_extravios_mascota
        WHERE id_mascota = ?
          AND estado = 'reportada'
          AND fecha_cierre IS NULL
        LIMIT 1
    ";
    $stmt2 = $pdo->prepare($sqlExtravio);
    $stmt2->execute([$mascota['id']]);
    $extravio = $stmt2->fetch(PDO::FETCH_ASSOC);

    $id_extravio = $extravio['id_extravio'] ?? null;

    /* =====================================================
       6️⃣ Respuesta pública segura
    ===================================================== */
    echo json_encode([
        'encontrada'   => true,
        'id_mascota'   => (int)$mascota['id'],
        'nombre'       => $mascota['nombre'],
        'raza'         => $mascota['raza'],
        'ciudad'       => $mascota['ciudad'],
        'extraviada'   => $id_extravio !== null,
        'id_extravio'  => $id_extravio
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno']);
}
