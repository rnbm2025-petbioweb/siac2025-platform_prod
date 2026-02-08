<?php
/*************************************************
 * ALERTA DE POSIBLE PÉRDIDA DE MASCOTA
 * Flujo público – SIN contacto directo
 *************************************************/

header('Content-Type: application/json');

$conn = new mysqli(
    "mysql_petbio_secure",
    "root",
    "R00t_Segura_2025!",
    "db__produccion_petbio_segura_2025",
    3306
);

if ($conn->connect_error) {
    http_response_code(500);
    exit(json_encode(['error' => 'Error de conexión']));
}

/*************************************************
 * VALIDAR DATOS
 *************************************************/
$id_mascota = (int)($_POST['id_mascota'] ?? 0);
$nombre     = trim($_POST['nombre'] ?? '');
$email      = trim($_POST['email'] ?? '');
$telefono   = trim($_POST['telefono'] ?? '');
$direccion  = trim($_POST['direccion'] ?? '');
$mensaje    = trim($_POST['mensaje'] ?? '');

if (!$id_mascota || !$nombre) {
    http_response_code(400);
    exit(json_encode(['error' => 'Datos incompletos']));
}

/*************************************************
 * GUARDAR ALERTA
 *************************************************/
$stmt = $conn->prepare("
  INSERT INTO posible_caso_perdida_de_mascota
  (
    id_mascota,
    nombre_reportante,
    email_reportante,
    telefono_reportante,
    direccion_aproximada,
    mensaje
  )
  VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssss",
    $id_mascota,
    $nombre,
    $email,
    $telefono,
    $direccion,
    $mensaje
);

$stmt->execute();
$stmt->close();

/*************************************************
 * RESPUESTA
 *************************************************/
echo json_encode([
    'status' => 'ok',
    'mensaje' => 'Gracias. Avisaremos al tutor de forma segura.'
]);
