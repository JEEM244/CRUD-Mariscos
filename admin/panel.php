<?php
// Inicio la sesión para comprobar que el administrador tenga acceso.
session_start();

// Si no hay una sesión activa, regreso al formulario de acceso.
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

// Cargo la conexión para consultar los datos que se muestran en el panel.
require_once "../config/conexion.php";

// Consulto cuántas mesas están registradas.
$consulta = $conexion->query("SELECT COUNT(*) FROM mesas");
$totalMesas = (int) $consulta->fetchColumn();

// Cuento los pedidos que todavía no se han pagado.
$consulta = $conexion->query("SELECT COUNT(*) FROM pedidos WHERE estado <> 'Pagado'");
$pedidosPendientes = (int) $consulta->fetchColumn();

// Cuento los platillos que están disponibles.
$consulta = $conexion->query("SELECT COUNT(*) FROM platillos WHERE disponible = 1");
$platillosDisponibles = (int) $consulta->fetchColumn();

// Consulto los últimos movimientos para mostrar actividad reciente.
$sql = "SELECT pedidos.id, pedidos.fecha, pedidos.estado, pedidos.total,
               mesas.numero AS numero_mesa
        FROM pedidos
        INNER JOIN mesas ON pedidos.mesa_id = mesas.id
        ORDER BY pedidos.id DESC
        LIMIT 5";
$consulta = $conexion->query($sql);
$pedidosRecientes = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de operaciones | MARISCOS PIPE</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <!-- La navegación lateral queda fija y separada del área de trabajo. -->
    <header class="topbar">
        <a class="logo-admin" href="panel.php">
            MARISCOS PIPE
            <small>SISTEMA DE OPERACIONES</small>
        </a>

        <nav>
            <a href="panel.php">Resumen general</a>
            <a href="pedidos.php">Pedidos</a>
            <a href="cobros.php">Cobros</a>
            <a href="platillos.php">Menú y platillos</a>
            <a href="modulo.php?tabla=clientes">Clientes</a>
            <a href="modulo.php?tabla=proveedores">Proveedores</a>
            <a href="modulo.php?tabla=almacen">Almacén</a>
            <a href="modulo.php?tabla=facturacion">Facturación</a>
        </nav>

        <div class="sidebar-note">
            <div>Administración local</div>
            <a href="index.php?salir=1">Cerrar sesión</a>
        </div>
    </header>

    <main class="wrap">
        <p class="eyebrow">MARISCOS PIPE / OPERACIONES</p>
        <div class="dashboard-heading">
            <div>
                <h1>Resumen general</h1>
                <p class="intro">Información del restaurante y acceso a las operaciones principales.</p>
            </div>
            <span class="dashboard-date">PANEL ADMINISTRATIVO</span>
        </div>

        <!-- Los indicadores se muestran como una sola franja de datos. -->
        <section class="stats">
            <article>
                <small>Mesas registradas</small>
                <strong><?= $totalMesas ?></strong>
            </article>
            <article>
                <small>Pedidos pendientes</small>
                <strong><?= $pedidosPendientes ?></strong>
            </article>
            <article>
                <small>Platillos disponibles</small>
                <strong><?= $platillosDisponibles ?></strong>
            </article>
        </section>

        <section class="dashboard">
            <div class="dashboard-main">
                <p class="eyebrow">SEGUIMIENTO</p>
                <h2 class="section-title" style="margin-top:0">Actividad reciente</h2>

                <div class="activity-list">
                    <?php foreach ($pedidosRecientes as $pedido): ?>
                        <div class="activity-row">
                            <div>
                                <strong>Pedido #<?= (int) $pedido["id"] ?> · Mesa <?= (int) $pedido["numero_mesa"] ?></strong>
                                <small>
                                    <?= htmlspecialchars($pedido["fecha"]) ?>
                                    · <?= htmlspecialchars($pedido["estado"]) ?>
                                </small>
                            </div>
                            <span class="activity-value">
                                $<?= number_format((float) $pedido["total"], 2) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($pedidosRecientes) === 0): ?>
                        <p class="nota-admin">Todavía no hay pedidos registrados.</p>
                    <?php endif; ?>
                </div>

                <h2 class="section-title">Administración de datos</h2>
                <section class="module-grid">
                    <a class="module-card" href="platillos.php">
                        <span>01 / CATÁLOGO</span>
                        <h2>Platillos</h2>
                        <p>Precios, categorías y disponibilidad.</p>
                        <b>Administrar →</b>
                    </a>
                    <a class="module-card" href="modulo.php?tabla=clientes">
                        <span>02 / REGISTROS</span>
                        <h2>Clientes</h2>
                        <p>Información de los clientes.</p>
                        <b>Administrar →</b>
                    </a>
                    <a class="module-card" href="modulo.php?tabla=proveedores">
                        <span>03 / REGISTROS</span>
                        <h2>Proveedores</h2>
                        <p>Contactos y productos suministrados.</p>
                        <b>Administrar →</b>
                    </a>
                    <a class="module-card" href="modulo.php?tabla=almacen">
                        <span>04 / INVENTARIO</span>
                        <h2>Almacén</h2>
                        <p>Existencias, unidades y costos.</p>
                        <b>Administrar →</b>
                    </a>
                    <a class="module-card" href="modulo.php?tabla=facturacion">
                        <span>05 / DOCUMENTOS</span>
                        <h2>Facturación</h2>
                        <p>Datos y solicitudes de facturación.</p>
                        <b>Administrar →</b>
                    </a>
                    <a class="module-card" href="cobros.php">
                        <span>06 / CAJA</span>
                        <h2>Cobros</h2>
                        <p>Consulta de cuentas y registro de pagos.</p>
                        <b>Administrar →</b>
                    </a>
                </section>
            </div>

            <aside class="dashboard-side">
                <p class="eyebrow">ACCESO RÁPIDO</p>
                <h2>Operaciones del día</h2>
                <p>Accesos directos para atender las mesas.</p>
                <div class="quick-links">
                    <a href="pedidos.php">Registrar un pedido <span>01 →</span></a>
                    <a href="cobros.php">Consultar cobros <span>02 →</span></a>
                    <a href="../index.php">Ver menú público <span>03 →</span></a>
                </div>
            </aside>
        </section>
    </main>

    <script src="../assets/js/tema.js" defer></script>
</body>
</html>
