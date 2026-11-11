<?php
// Primero inicio la sesión para conservar el estado de la demostración entre páginas.
session_start();

// Ahora compruebo que el usuario haya iniciado sesión como administrador.
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

// Ahora cargo la conexión porque las solicitudes de prueba se guardan en MySQL.
require_once "../config/conexion.php";

// Este apartado es una demostración escolar: no se conecta al SAT ni genera facturas reales.
$mensaje = "";
$error = "";
$folioDemo = "";

// Aquí reviso si se presionó el botón que activa la conexión ficticia.
if (isset($_POST["probar_conexion"])) {
    $_SESSION["hacienda_demo"] = true;
    $mensaje = "La conexión de prueba está activa. No se contactó al SAT.";
}

// Aquí reviso si se presionó el botón para cerrar la demostración.
if (isset($_POST["desconectar"])) {
    unset($_SESSION["hacienda_demo"]);
    $mensaje = "La conexión de prueba se cerró.";
}

// Ahora recibo los datos del formulario para guardar una solicitud de facturación de ejemplo.
if (isset($_POST["generar_demo"])) {
    // Ahora compruebo que primero se haya activado la conexión simulada.
    if (empty($_SESSION["hacienda_demo"])) {
        $error = "Primero activa la conexión de prueba.";
    } else {
        // Ahora leo los datos del formulario. Se convierten o limpian antes de utilizarlos.
        $clienteId = (int) ($_POST["cliente_id"] ?? 0);
        $rfc = strtoupper(trim($_POST["rfc"] ?? ""));
        $razonSocial = trim($_POST["razon_social"] ?? "");
        $codigoPostal = trim($_POST["codigo_postal"] ?? "");
        $usoCfdi = $_POST["uso_cfdi"] ?? "G03";
        $total = (float) ($_POST["total"] ?? 0);

        // Ahora compruebo que se eligió un cliente y que los datos obligatorios tengan un formato básico.
        if (
            $clienteId <= 0 ||
            $rfc === "" ||
            $razonSocial === "" ||
            !preg_match("/^[0-9]{5}$/", $codigoPostal) ||
            $total <= 0
        ) {
            $error = "Revisa los datos. El código postal debe tener cinco números y el total debe ser mayor a cero.";
        } else {
            // Ahora preparo el INSERT que guarda la solicitud como Solicitada, no como factura emitida.
            $sql = "INSERT INTO facturacion
                        (cliente_id, rfc, razon_social, codigo_postal, uso_cfdi, total, estado)
                    VALUES (?, ?, ?, ?, ?, ?, 'Solicitada')";

            // Aquí preparo la consulta para enviar los datos de forma segura.
            $guardar = $conexion->prepare($sql);
            // Después envío los valores en el mismo orden en que aparecen los signos de interrogación del INSERT.
            $guardar->execute([
                $clienteId,
                $rfc,
                $razonSocial,
                $codigoPostal,
                $usoCfdi,
                $total
            ]);

            // Después recupero el ID que MySQL asignó al registro que se acaba de guardar.
            $idSolicitud = $conexion->lastInsertId();
            $folioDemo = "DEMO-HAC-" . str_pad($idSolicitud, 5, "0", STR_PAD_LEFT);

            $_SESSION["ultimo_folio_demo"] = $folioDemo;
            $mensaje = "La solicitud se guardó. Recuerda que no es una factura fiscal.";
        }
    }
}

// Ahora obtengo los clientes para mostrarlos en el selector del formulario.
$consulta = $conexion->query("SELECT id, nombre FROM clientes ORDER BY nombre");
$clientes = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Ahora consulto las últimas solicitudes junto con el nombre del cliente relacionado.
$sql = "SELECT facturacion.*, clientes.nombre AS nombre_cliente
        FROM facturacion
        LEFT JOIN clientes ON facturacion.cliente_id = clientes.id
        ORDER BY facturacion.id DESC
        LIMIT 8";

$consulta = $conexion->query($sql);
$solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Si se actualizó la página, recuperar el último folio guardado en la sesión.
if ($folioDemo === "" && !empty($_SESSION["ultimo_folio_demo"])) {
    $folioDemo = $_SESSION["ultimo_folio_demo"];
}
?>
<!-- Aquí desde aquí comienza la estructura HTML que se muestra en el navegador. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hacienda / Facturación | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
        <!-- Aquí encabezado con enlaces para volver al panel o cerrar sesión. -->
    <header class="topbar">
        <a class="logo-admin" href="panel.php">
            EL PUERTO
            <small>ADMINISTRACIÓN</small>
        </a>

        <nav>
            <a href="panel.php">Panel</a>
            <a href="modulo.php?tabla=facturacion">Facturación</a>
            <a href="index.php?salir=1">Cerrar sesión</a>
        </nav>
    </header>

        <!-- Aquí contenido principal del módulo de simulación. -->
    <main class="wrap">
        <p class="eyebrow">FACTURACIÓN</p>
        <h1>Conexión con Hacienda</h1>
        <p class="intro">
            Esta pantalla representa cómo podría funcionar el envío de datos
            a un servicio externo para solicitar una factura.
        </p>

        <div class="notice">
            Esta es una simulación para el proyecto escolar. No se conecta al SAT,
            no genera CFDI y no tiene validez fiscal.
        </div>

                <!-- Aquí panel que muestra si la conexión ficticia está activa o apagada. -->
        <section class="panel-contenido">
            <h2>Estado de la conexión</h2>
            <p>Servicio: Hacienda / SAT (representación)</p>

            <p>
                Estado:
                <?php if (!empty($_SESSION["hacienda_demo"])): ?>
                    <strong class="estado-conectado">Conectado (simulación)</strong>
                <?php else: ?>
                    <strong class="estado-desconectado">Desconectado</strong>
                <?php endif; ?>
            </p>

            <form method="POST">
                <?php if (empty($_SESSION["hacienda_demo"])): ?>
                    <button class="boton-principal" type="submit" name="probar_conexion">
                        Probar conexión
                    </button>
                <?php else: ?>
                    <button class="boton-secundario" type="submit" name="desconectar">
                        Desconectar prueba
                    </button>
                <?php endif; ?>
            </form>
        </section>

        <?php if ($mensaje !== ""): ?>
            <p class="notice"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <p class="notice"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

                <!-- Aquí formulario para capturar la información que se guardará como solicitud de prueba. -->
        <!-- Aquí formulario con los datos que se guardarán en la tabla de facturación. -->
        <section class="panel-contenido">
            <h2>Enviar datos para facturación</h2>
            <p class="intro">
                Llena los datos como si el sistema preparara una solicitud para el servicio externo.
            </p>

            <form method="POST">
                <div class="form-grid">
                    <label>
                        Cliente
                        <select name="cliente_id" required>
                            <option value="">Selecciona un cliente</option>

                                                        <!-- Aquí llenar el selector con los clientes que existen en la base de datos. -->
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= (int) $cliente["id"] ?>">
                                    <?= htmlspecialchars($cliente["nombre"]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        RFC
                        <input
                            type="text"
                            name="rfc"
                            maxlength="13"
                            placeholder="RFC del cliente"
                            required
                        >
                    </label>

                    <label>
                        Razón social
                        <input type="text" name="razon_social" required>
                    </label>

                    <label>
                        Código postal
                        <input
                            type="text"
                            name="codigo_postal"
                            maxlength="5"
                            inputmode="numeric"
                            placeholder="00000"
                            required
                        >
                    </label>

                    <label>
                        Uso de CFDI
                        <select name="uso_cfdi">
                            <option value="G03">G03 - Gastos en general</option>
                            <option value="G01">G01 - Adquisición de mercancías</option>
                            <option value="P01">P01 - Por definir</option>
                        </select>
                    </label>

                    <label>
                        Total de la venta
                        <input
                            type="number"
                            name="total"
                            min="0.01"
                            step="0.01"
                            placeholder="0.00"
                            required
                        >
                    </label>
                </div>

                <button class="boton-principal" type="submit" name="generar_demo">
                    Enviar solicitud de prueba
                </button>
            </form>
        </section>

                <!-- Aquí la respuesta solo aparece cuando ya se generó una solicitud de prueba. -->
        <?php if ($folioDemo !== ""): ?>
            <section class="panel-contenido">
                <h2>Respuesta de la prueba</h2>
                <p><strong>Solicitud recibida (simulación)</strong></p>
                <p>Folio interno: <strong><?= htmlspecialchars($folioDemo) ?></strong></p>
                <p>Respuesta de ejemplo: "Los datos fueron recibidos para revisión".</p>
                <p>No se creó XML fiscal, sello digital ni timbre del SAT.</p>
            </section>
        <?php endif; ?>

        <h2 class="section-title">Solicitudes registradas</h2>

        <!-- Aquí tabla con las solicitudes de prueba que ya se guardaron. -->
        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Folio interno</th>
                        <th>Cliente</th>
                        <th>RFC</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>

                <tbody>
                                        <!-- Aquí mostrar cada solicitud guardada con su folio interno. -->
                    <?php foreach ($solicitudes as $solicitud): ?>
                        <tr>
                            <td>DEMO-HAC-<?= str_pad($solicitud["id"], 5, "0", STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($solicitud["nombre_cliente"] ?? "Sin cliente") ?></td>
                            <td><?= htmlspecialchars($solicitud["rfc"] ?? "") ?></td>
                            <td>$<?= number_format((float) $solicitud["total"], 2) ?></td>
                            <td><?= htmlspecialchars($solicitud["estado"]) ?></td>
                            <td><?= htmlspecialchars($solicitud["fecha"]) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($solicitudes) === 0): ?>
                        <tr>
                            <td colspan="6">Todavía no hay solicitudes registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
