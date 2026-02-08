<?php
// contactar_extravio.php

$host = "mysql_petbio_secure";
$usuario = "root";
$clave = "R00t_Segura_2025!";
$dbname = "db__produccion_petbio_segura_2025";
$port = 3306;

$conn = new mysqli($host, $usuario, $clave, $dbname, $port);
if ($conn->connect_error) {
    die("Error de conexión");
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

/* =====================================================
   PROCESAR FORMULARIO (POST)
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_extravio = (int)($_POST['id_extravio'] ?? 0);
    $nombre   = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $mensaje  = trim($_POST['mensaje'] ?? '');

    if (!$id_extravio || $nombre === '' || ($telefono === '' && $email === '')) {
        die("Datos incompletos");
    }

    // 1️⃣ Verificar que el extravío siga activo
    $sql = "
        SELECT 1
        FROM registro_extravios_mascota
        WHERE id_extravio = ?
        AND estado = 'reportada'
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_extravio);
    $stmt->execute();
    $activo = $stmt->get_result()->num_rows;
    $stmt->close();

    if (!$activo) {
        die("Este reporte ya no está activo");
    }

    // 2️⃣ Rate limit por IP (5 mensajes / 24h)
    $sqlRate = "
        SELECT COUNT(*) total
        FROM contactos_extravio
        WHERE ip_contacto = ?
        AND fecha_contacto >= NOW() - INTERVAL 1 DAY
    ";
    $stmt = $conn->prepare($sqlRate);
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    if ($total >= 5) {
        die("Has alcanzado el límite de mensajes por hoy");
    }

    // 3️⃣ Guardar contacto
    $sqlInsert = "
        INSERT INTO contactos_extravio
        (id_extravio, nombre_contactante, telefono, email, mensaje, ip_contacto)
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param(
        "isssss",
        $id_extravio,
        $nombre,
        $telefono,
        $email,
        $mensaje,
        $ip
    );
    $stmt->execute();
    $stmt->close();
    $conn->close();
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8">
      <title>Mensaje enviado · PetBio</title>
      <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-green-50 min-h-screen flex items-center justify-center p-6">
      <div class="bg-white max-w-md w-full p-6 rounded-xl shadow text-center">
        <h1 class="text-2xl font-bold text-green-600 mb-3">✅ Mensaje enviado</h1>
        <p class="text-gray-700 mb-4">
          El cuidador de la mascota recibirá tu información.
        </p>
        <p class="text-sm text-gray-500">Gracias por ayudar 💚</p>
      </div>
    </body>
    </html>
    <?php
    exit;
}

/* =====================================================
   MOSTRAR FORMULARIO (GET)
===================================================== */


$id_extravio = (int)($_GET['id_extravio'] ?? 0);
if (!$id_extravio) {
    die("Solicitud inválida");
}

// Verificar que el extravío siga activo
$sql = "
    SELECT 1
    FROM registro_extravios_mascota
    WHERE id_extravio = ?
      AND estado = 'reportada'
      AND fecha_cierre IS NULL
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_extravio);
$stmt->execute();
$activo = $stmt->get_result()->num_rows;
$stmt->close();

if (!$activo) {
    die("Este reporte de extravío no existe o ya fue cerrado");
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Contactar por Mascota Extraviada</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        petbioazul: '#27445D',
        petbioazulclaro: '#72BCB3',
        petbioverde: '#497D74',
        petbiofondo: '#EFE9D5'
      }
    }
  }
}
</script>
</head>

<body class="bg-petbiofondo min-h-screen flex items-center justify-center p-6">

<div class="bg-white max-w-lg w-full p-6 rounded-xl shadow">
  <h2 class="text-xl font-bold text-red-600 mb-4 text-center">
    🚨 ¿Encontraste esta mascota?
  </h2>

  <form method="POST" class="space-y-4">
    <input type="hidden" name="id_extravio" value="<?= $id_extravio ?>">

    <div>
      <label class="block font-medium mb-1">Tu nombre *</label>
      <input type="text" name="nombre" required
        class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block font-medium mb-1">Teléfono</label>
      <input type="text" name="telefono"
        class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block font-medium mb-1">Correo electrónico</label>
      <input type="email" name="email"
        class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block font-medium mb-1">Mensaje</label>
      <textarea name="mensaje" rows="4"
        class="w-full border rounded px-3 py-2"
        placeholder="Dónde viste la mascota, hora, estado, etc."></textarea>
    </div>

    <button class="w-full bg-petbioazulclaro hover:bg-petbioverde text-white font-bold py-3 rounded">
      📩 Enviar mensaje
    </button>
  </form>

  <p class="mt-4 text-xs text-gray-500 text-center">
    PetBio protege tus datos. El contacto será usado solo para esta búsqueda.
  </p>
</div>

</body>
</html>
<?php

