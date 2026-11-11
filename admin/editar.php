<?php
session_start();

if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

require_once "../config/conexion.php";

// Obtener el ID que llega en la dirección.
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: platillos.php");
    exit;
}

// Buscar el platillo que se va a editar.
$consulta = $conexion->prepare("SELECT * FROM platillos WHERE id = ?");
$consulta->execute([$id]);
$platillo = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$platillo) {
    header("Location: platillos.php");
    exit;
}

// Cargar las categorías del menú.
$consulta = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $consulta->fetchAll(PDO::FETCH_ASSOC);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = $_POST["precio"] ?? "";
    $imagen = trim($_POST["imagen"] ?? "");
    $categoriaId = $_POST["categoria"] ?? "";
    $disponible = isset($_POST["disponible"]) ? 1 : 0;

    if ($nombre === "" || !is_numeric($precio) || (float) $precio < 0) {
        $error = "Escribe el nombre del platillo y un precio válido.";
    } else {
        $sql = "UPDATE platillos
                SET nombre = ?, descripcion = ?, precio = ?,
                    imagen = ?, categoria_id = ?, disponible = ?
                WHERE id = ?";

        $actualizar = $conexion->prepare($sql);
        $actualizar->execute([
            $nombre,
            $descripcion,
            $precio,
            $imagen,
            $categoriaId !== "" ? $categoriaId : null,
            $disponible,
            $id
        ]);

        header("Location: platillos.php?mensaje=" . urlencode("Platillo actualizado."));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar platillo | El Puerto</title>
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
        <h1>Editar platillo</h1>

        <?php if ($error !== ""): ?>
            <p class="mensaje-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>
                Nombre del platillo
                <input
                    type="text"
                    name="nombre"
                    maxlength="120"
                    value="<?= htmlspecialchars($platillo["nombre"]) ?>"
                    required
                >
            </label>

            <label>
                Descripción
                <textarea name="descripcion" rows="4"><?= htmlspecialchars($platillo["descripcion"] ?? "") ?></textarea>
            </label>

            <label>
                Precio (MXN)
                <input
                    type="number"
                    name="precio"
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars((string) $platillo["precio"]) ?>"
                    required
                >
            </label>

            <label>
                Categoría
                <select name="categoria">
                    <option value="">Sin categoría</option>

                    <?php foreach ($categorias as $categoria): ?>
                        <option
                            value="<?= (int) $categoria["id"] ?>"
                            <?= $platillo["categoria_id"] == $categoria["id"] ? "selected" : "" ?>
                        >
                            <?= htmlspecialchars($categoria["nombre"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Dirección de la imagen (opcional)
                <input
                    type="url"
                    name="imagen"
                    value="<?= htmlspecialchars($platillo["imagen"] ?? "") ?>"
                >
            </label>

            <label class="check-line">
                <input
                    type="checkbox"
                    name="disponible"
                    <?= $platillo["disponible"] ? "checked" : "" ?>
                >
                Mostrar en el menú público
            </label>

            <button class="boton boton-coral" type="submit">Guardar cambios</button>
            <a class="volver" href="platillos.php">Cancelar</a>
        </form>
    </main>
</body>
</html>
