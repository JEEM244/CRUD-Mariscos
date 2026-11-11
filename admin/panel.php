<?php
session_start();
if (empty($_SESSION["admin"])) { header("Location: index.php"); exit; }
require_once "../config/conexion.php";
$mesas = $conexion->query("SELECT COUNT(*) FROM mesas")->fetchColumn();
$pedidos = $conexion->query("SELECT COUNT(*) FROM pedidos WHERE estado <> 'Pagado'")->fetchColumn();
$platillos = $conexion->query("SELECT COUNT(*) FROM platillos WHERE disponible=1")->fetchColumn();
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Panel | El Puerto</title><link rel="stylesheet" href="../assets/css/estilos.css"></head><body class="pixel-bg">
<header class="topbar"><a class="pixel-logo" href="panel.php">EL PUERTO <small>SEAFOOD SYSTEM</small></a><nav><a href="../index.php">Ver menú</a><a href="index.php?salir=1">Salir</a></nav></header>
<main class="wrap"><p class="eyebrow">PANEL PRINCIPAL / PLAYER 01</p><h1>Centro de operaciones</h1><p class="intro">Administra pedidos, cobros y datos del restaurante.</p>
<section class="stats"><article><small>MESAS REGISTRADAS</small><strong><?= $mesas ?></strong></article><article><small>PEDIDOS ABIERTOS</small><strong><?= $pedidos ?></strong></article><article><small>PLATILLOS ACTIVOS</small><strong><?= $platillos ?></strong></article></section>
<h2 class="section-title">MÓDULOS</h2><section class="module-grid">
<a class="module-card highlight" href="pedidos.php"><span>01</span><h2>Nuevo pedido</h2><p>Elegir mesa, comida y cantidades.</p><b>INICIAR →</b></a>
<a class="module-card" href="cobros.php"><span>02</span><h2>Cobros</h2><p>Consultar cuentas y registrar pagos.</p><b>ABRIR →</b></a>
<a class="module-card" href="modulo.php?tabla=clientes"><span>02</span><h2>Clientes</h2><p>Altas, consultas, cambios y bajas.</p><b>ABRIR →</b></a>
<a class="module-card" href="modulo.php?tabla=proveedores"><span>03</span><h2>Proveedores</h2><p>Directorio de proveedores.</p><b>ABRIR →</b></a>
<a class="module-card" href="hacienda.php"><span>05</span><h2>Hacienda / SAT</h2><p>Simular envío de datos para facturación.</p><b>PROBAR →</b></a>
<a class="module-card" href="modulo.php?tabla=facturacion"><span>06</span><h2>Facturación</h2><p>Datos fiscales y estado de factura.</p><b>ABRIR →</b></a>
<a class="module-card" href="modulo.php?tabla=almacen"><span>07</span><h2>Almacén</h2><p>Existencias y costos de productos.</p><b>ABRIR →</b></a>
<a class="module-card" href="platillos.php"><span>08</span><h2>Menú / precios</h2><p>Administrar platillos y disponibilidad.</p><b>ABRIR →</b></a>
</section></main><footer class="pixel-footer">EL PUERTO © <?= date("Y") ?> <span>PRESS START TO WORK</span></footer></body></html>