<?php
session_start();
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

require_once "../config/conexion.php";

$mesas = $conexion->query("SELECT COUNT(*) FROM mesas")->fetchColumn();
$pedidos = $conexion->query("SELECT COUNT(*) FROM pedidos WHERE estado <> 'Pagado'")->fetchColumn();
$platillos = $conexion->query("SELECT COUNT(*) FROM platillos WHERE disponible = 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel principal | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <header class="topbar">
        <a class="pixel-logo" href="panel.php">EL PUERTO <small>ADMINISTRACIÓN</small></a>
        <nav>
            <a href="../index.php">Ver menú</a>
            <a href="index.php?salir=1">Cerrar sesión</a>
        </nav>
    </header>

    <main class="wrap">
        <p class="eyebrow">ADMINISTRACIÓN</p>
        <h1>Panel principal</h1>
        <p class="intro">Desde aquí puedes administrar los pedidos, los cobros y la información del restaurante.</p>

        <section class="stats">
            <article>
                <small>Mesas registradas</small>
                <strong><?= $mesas ?></strong>
            </article>
            <article>
                <small>Pedidos pendientes</small>
                <strong><?= $pedidos ?></strong>
            </article>
            <article>
                <small>Platillos disponibles</small>
                <strong><?= $platillos ?></strong>
            </article>
        </section>

        <h2 class="section-title">Módulos</h2>
        <section class="module-grid">
            <a class="module-card highlight" href="pedidos.php">
                <span>01</span>
                <h2>Nuevo pedido</h2>
                <p>Seleccionar mesa, platillos y cantidades.</p>
                <b>Abrir módulo</b>
            </a>
            <a class="module-card" href="cobros.php">
                <span>02</span>
                <h2>Cobros</h2>
                <p>Consultar cuentas y registrar pagos.</p>
                <b>Abrir módulo</b>
            </a>
            <a class="module-card" href="platillos.php">
                <span>03</span>
                <h2>Menú y precios</h2>
                <p>Administrar los platillos disponibles.</p>
                <b>Abrir módulo</b>
            </a>
            <a class="module-card" href="modulo.php?tabla=clientes">
                <span>04</span>
                <h2>Clientes</h2>
                <p>Registrar y consultar clientes.</p>
                <b>Abrir módulo</b>
            </a>
            <a class="module-card" href="modulo.php?tabla=proveedores">
                <span>05</span>
                <h2>Proveedores</h2>
                <p>Guardar los datos de los proveedores.</p>
                <b>Abrir módulo</b>
            </a>
            <a class="module-card" href="modulo.php?tabla=almacen">
                <span>06</span>
                <h2>Almacén</h2>
                <p>Revisar productos, existencias y costos.</p>
                <b>Abrir módulo</b>
            </a>
            <a class="module-card" href="modulo.php?tabla=facturacion">
                <span>07</span>
                <h2>Facturación</h2>
                <p>Consultar los datos de facturación.</p>
                <b>Abrir módulo</b>
            </a>
            <a class="module-card" href="hacienda.php">
                <span>08</span>
                <h2>Hacienda / SAT</h2>
                <p>Probar el proceso de facturación simulado.</p>
                <b>Abrir módulo</b>
            </a>
        </section>
    </main>

    <footer class="pixel-footer">
        <span>Mariscos El Puerto © <?= date("Y") ?></span>
        <span>Panel de administración</span>
    </footer>
</body>
</html>
