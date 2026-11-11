<?php
session_start();
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}
require_once "../config/conexion.php";

// Esta pantalla representa una conexión imaginaria para fines escolares.
// No realiza peticiones al SAT ni genera CFDI reales.
$mensaje = "";
$folioDemo = "";
$error = "";

if (isset($_POST["probar_conexion"])) {
    $_SESSION["hacienda_demo"] = true;
    $mensaje = "Conexión de demostración establecida. No se contactó al SAT.";
}

if (isset($_POST["desconectar"])) {
    unset($_SESSION["hacienda_demo"]);
    $mensaje = "La conexión de demostración se cerró.";
}

if (isset($_POST["generar_demo"])) {
    if (empty($_SESSION["hacienda_demo"])) {
        $error = "Primero activa la conexión de demostración.";
    } else {
        $clienteId = (int)($_POST["cliente_id"] ?? 0);
        $rfc = strtoupper(trim($_POST["rfc"] ?? ""));
        $razon = trim($_POST["razon_social"] ?? "");
        $cp = trim($_POST["codigo_postal"] ?? "");
        $uso = trim($_POST["uso_cfdi"] ?? "G03");
        $total = (float)($_POST["total"] ?? 0);

        if ($clienteId <= 0 || $rfc === "" || $razon === "" || $cp === "" || $total <= 0) {
            $error = "Completa todos los datos y revisa que el total sea mayor a cero.";
        } else {
            $guardar = $conexion->prepare(
                "INSERT INTO facturacion (cliente_id, rfc, razon_social, codigo_postal, uso_cfdi, total, estado)
                 VALUES (?, ?, ?, ?, ?, ?, 'Solicitada')"
            );
            $guardar->execute([$clienteId, $rfc, $razon, $cp, $uso, $total]);
            $id = $conexion->lastInsertId();
            $folioDemo = "DEMO-HAC-" . str_pad($id, 5, "0", STR_PAD_LEFT);
            $_SESSION["ultimo_folio_demo"] = $folioDemo;
            $mensaje = "Solicitud de prueba registrada. No es una factura fiscal.";
        }
    }
}

$clientes = $conexion->query("SELECT id, nombre FROM clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$solicitudes = $conexion->query(
    "SELECT f.*, c.nombre AS cliente
     FROM facturacion f
     LEFT JOIN clientes c ON c.id = f.cliente_id
     ORDER BY f.id DESC LIMIT 8"
)->fetchAll(PDO::FETCH_ASSOC);

if ($folioDemo === "" && !empty($_SESSION["ultimo_folio_demo"])) {
    $folioDemo = $_SESSION["ultimo_folio_demo"];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hacienda / Facturación | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body class="pixel-bg">
<header class="topbar">
    <a class="pixel-logo" href="panel.php">EL PUERTO <small>ADMINISTRACIÓN</small></a>
    <nav><a href="panel.php">Panel</a><a href="modulo.php?tabla=facturacion">Facturación</a><a href="index.php?salir=1">Salir</a></nav>
</header>

<main class="wrap">
    <p class="eyebrow">MÓDULO EXPERIMENTAL / CONEXIÓN EXTERNA</p>
    <h1>Conexión con Hacienda</h1>
    <p class="intro">Esta sección muestra cómo un estudiante imagina que el sistema podría enviar los datos de una venta a un servicio de Hacienda para solicitar una factura.</p>

    <div class="notice">
        MODO ESCOLAR: todo es una simulación. No existe conexión real con el SAT, no se timbran CFDI y los comprobantes de esta pantalla no tienen validez fiscal.
    </div>

    <section class="pixel-panel">
        <h2>01 / ESTADO DE LA CONEXIÓN</h2>
        <p>Servicio externo: <strong>Hacienda / SAT (representación)</strong></p>
        <p>Estado:
            <?php if (!empty($_SESSION["hacienda_demo"])): ?>
                <strong style="color:var(--cyan)">● CONECTADO — SIMULACIÓN</strong>
            <?php else: ?>
                <strong style="color:var(--pink)">● DESCONECTADO</strong>
            <?php endif; ?>
        </p>
        <form method="POST">
            <?php if (empty($_SESSION["hacienda_demo"])): ?>
                <button class="pixel-button" name="probar_conexion" type="submit">PROBAR CONEXIÓN</button>
            <?php else: ?>
                <button class="small-btn" name="desconectar" type="submit">DESCONECTAR DEMO</button>
            <?php endif; ?>
        </form>
    </section>

    <?php if ($mensaje !== ""): ?><p class="notice"><?= htmlspecialchars($mensaje) ?></p><?php endif; ?>
    <?php if ($error !== ""): ?><p class="notice"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <section class="pixel-panel">
        <h2>02 / ENVIAR DATOS PARA FACTURACIÓN</h2>
        <p class="intro">Llena los datos como si el sistema preparara una solicitud para el servicio externo.</p>
        <form method="POST">
            <div class="form-grid">
                <label>Cliente
                    <select name="cliente_id" required>
                        <option value="">Selecciona un cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente["id"] ?>"><?= htmlspecialchars($cliente["nombre"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>RFC
                    <input name="rfc" maxlength="13" placeholder="RFC del cliente" required>
                </label>
                <label>Razón social
                    <input name="razon_social" placeholder="Nombre fiscal" required>
                </label>
                <label>Código postal
                    <input name="codigo_postal" maxlength="5" placeholder="00000" required>
                </label>
                <label>Uso de CFDI
                    <select name="uso_cfdi">
                        <option value="G03">G03 - Gastos en general</option>
                        <option value="G01">G01 - Adquisición de mercancías</option>
                        <option value="P01">P01 - Por definir</option>
                    </select>
                </label>
                <label>Total de la venta
                    <input type="number" name="total" min="0.01" step="0.01" placeholder="0.00" required>
                </label>
            </div>
            <button class="pixel-button" type="submit" name="generar_demo">ENVIAR SOLICITUD DE PRUEBA</button>
        </form>
    </section>

    <?php if ($folioDemo !== ""): ?>
        <section class="pixel-panel">
            <h2>03 / RESPUESTA DEL SERVICIO (SIMULADA)</h2>
            <p><strong style="color:var(--cyan)">SOLICITUD RECIBIDA — SOLO DEMOSTRACIÓN</strong></p>
            <p>Folio interno: <strong><?= htmlspecialchars($folioDemo) ?></strong></p>
            <p>Respuesta imaginaria: "Los datos fueron recibidos para revisión".</p>
            <p>No se generó XML fiscal, sello digital ni timbre del SAT.</p>
        </section>
    <?php endif; ?>

    <h2 class="section-title">SOLICITUDES REGISTRADAS</h2>
    <div class="table-box">
        <table>
            <thead><tr><th>Folio interno</th><th>Cliente</th><th>RFC</th><th>Total</th><th>Estado</th><th>Fecha</th></tr></thead>
            <tbody>
            <?php foreach ($solicitudes as $solicitud): ?>
                <tr>
                    <td>DEMO-HAC-<?= str_pad($solicitud["id"], 5, "0", STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars($solicitud["cliente"] ?? "Sin cliente") ?></td>
                    <td><?= htmlspecialchars($solicitud["rfc"] ?? "") ?></td>
                    <td>$<?= number_format((float)$solicitud["total"], 2) ?></td>
                    <td><?= htmlspecialchars($solicitud["estado"]) ?></td>
                    <td><?= htmlspecialchars($solicitud["fecha"]) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($solicitudes) === 0): ?><tr><td colspan="6">Todavía no hay solicitudes.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>