<?php
session_start();
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

require_once "../config/conexion.php";

$buscar = trim($_GET["buscar"] ?? "");
$sql = "SELECT platillos.*, categorias.nombre AS categoria
        FROM platillos
        LEFT JOIN categorias ON platillos.categoria_id = categorias.id";
if ($buscar !== "") {
    $sql .= " WHERE platillos.nombre LIKE :buscar";
}
$sql .= " ORDER BY platillos.id DESC";

$consulta = $conexion->prepare($sql);
if ($buscar !== "") {
    $consulta->bindValue(":buscar", "%$buscar%");
}
$consulta->execute();
$platillos = $consulta->fetchAll(PDO::FETCH_ASSOC);

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$mensaje = $_GET["mensaje"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar platillos | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body class="admin-pagina">
    <header class="barra barra-admin">
        <a class="marca" href="../index.php"><span>EL PUERTO</span><small>ADMINISTRACIÓN</small></a>
        <nav><a href="../index.php">Ver sitio</a><a href="index.php?salir=1">Cerrar sesión</a></nav>
    </header>

    <main class="admin-contenido">
        <div class="encabezado-seccion">
            <div><p class="etiqueta">PANEL DE CONTROL</p><h1>Platillos</h1><p>Administra los productos que aparecen en el menú.</p></div>
            <a class="boton boton-coral" href="nuevo.php">+ Agregar platillo</a>
        </div>

        <?php if ($mensaje !== ""): ?><p class="mensaje-ok"><?= htmlspecialchars($mensaje) ?></p><?php endif; ?>

        <form class="buscador" method="GET">
            <input type="search" name="buscar" placeholder="Buscar platillo..." value="<?= htmlspecialchars($buscar) ?>">
            <button class="boton boton-azul" type="submit">Buscar</button>
            <a class="volver" href="platillos.php">Limpiar</a>
        </form>

        <div class="tabla-contenedor">
            <table>
                <thead><tr><th>Platillo</th><th>Categoría</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($platillos as $platillo): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($platillo["nombre"]) ?></strong></td>
                        <td><?= htmlspecialchars($platillo["categoria"] ?? "Sin categoría") ?></td>
                        <td>$<?= number_format((float)$platillo["precio"], 2) ?></td>
                        <td><?= $platillo["disponible"] ? "Disponible" : "Oculto" ?></td>
                        <td class="acciones">
                            <a href="editar.php?id=<?= $platillo["id"] ?>">Editar</a>
                            <form method="POST" action="eliminar.php" onsubmit="return confirm('¿Eliminar este platillo?')">
                                <input type="hidden" name="id" value="<?= $platillo["id"] ?>">
                                <button class="enlace-eliminar" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($platillos) === 0): ?><tr><td colspan="5">No se encontraron platillos.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="nota-admin">Total de platillos: <?= count($platillos) ?></p>
    </main>
</body>
</html>
