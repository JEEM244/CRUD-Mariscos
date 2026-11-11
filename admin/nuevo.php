<?php
session_start();

if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

require_once "../config/conexion.php";

// Cargar las categorías para mostrarlas en la lista.
$consulta = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $consulta->fetchAll(PDO::FETCH_ASSOC);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = $_POST["precio"] ?? "";
    $imagen = trim($_POST["imagen"] ?? "");
    $categoriaId = $_POST["categoria"] ?? "";

    if ($nombre === "" || !is_numeric($precio) || (float) $precio < 0) {
        $error = "Escribe el nombre del platillo y un precio válido.";
    } else {
        $sql = "INSERT INTO platillos
                    (nombre, descripcion, precio, imagen, categoria_id, disponible)
                VALUES (?, ?, ?, ?, ?, 1)";

        $guardar = $conexion->prepare($sql);
        $guardar->execute([
            $nombre,
            $descripcion,
            $precio,
            $imagen,
            $categoriaId !== "" ? $categoriaId : null
        ]);

        header("Location: platillos.php?mensaje=" . urlencode("Platillo agregado."));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar platillo | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <header class="barra barra-admin">
        <a class="marca" href="../index.php">
            <span>EL PUERTO</span>
            <small>ADMINISTRACIÓN</small>
        </a>

        <nav>
            <a href="panel.php">Panel</a>
            <a href="platillos.php">Volver a platillos</a>
        </nav>
    </header>

    <main class="formulario-admin">
        <p class="etiqueta">MENÚ</p>
        <h1>Agregar platillo</h1>

        <?php if ($error !== ""): ?>
            <p class="mensaje-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>
                Nombre del platillo
                <input type="text" name="nombre" maxlength="120" required>
            </label>

            <label>
                Descripción
                <textarea name="descripcion" rows="4"></textarea>
            </label>

            <label>
                Precio (MXN)
                <input type="number" name="precio" min="0" step="0.01" required>
            </label>

            <label>
                Categoría
                <select name="categoria">
                    <option value="">Sin categoría</option>

                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int) $categoria["id"] ?>">
                            <?= htmlspecialchars($categoria["nombre"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Dirección de la imagen (opcional)
                <input type="url" name="imagen" placeholder="https://ejemplo.com/platillo.jpg">
            </label>

            <button class="boton boton-coral" type="submit">Guardar platillo</button>
            <a class="volver" href="platillos.php">Cancelar</a>
        </form>
    </main>
</body>
</html>
