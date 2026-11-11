<?php
// Iniciar la sesión para comprobar que el usuario puede administrar el menú.
session_start();

// Si no existe una sesión activa, regresar al acceso.
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

// Cargar la conexión para consultar las categorías y guardar el nuevo platillo.
require_once "../config/conexion.php";

// Consultar las categorías para llenar el selector del formulario.
$consulta = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $consulta->fetchAll(PDO::FETCH_ASSOC);

$error = "";

// Leer los datos solamente cuando el formulario se haya enviado.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recibir cada campo y quitar espacios innecesarios del texto.
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = $_POST["precio"] ?? "";
    $imagen = trim($_POST["imagen"] ?? "");
    $categoriaId = $_POST["categoria"] ?? "";

    // Revisar que el nombre no esté vacío y que el precio sea un número válido.
    if ($nombre === "" || !is_numeric($precio) || (float) $precio < 0) {
        $error = "Escribe el nombre del platillo y un precio válido.";
    } else {
        // Preparar la consulta para crear el nuevo platillo en la tabla.
        $sql = "INSERT INTO platillos
                    (nombre, descripcion, precio, imagen, categoria_id, disponible)
                VALUES (?, ?, ?, ?, ?, 1)";

        // Preparar el INSERT y después enviar los valores del formulario.
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
<!-- Formulario que permite registrar un platillo nuevo. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar platillo | MARISCOS PIPE</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <header class="barra">
        <a class="marca" href="../index.php">
            <span>MARISCOS PIPE</span>
            <small>ADMINISTRACIÓN</small>
        </a>

        <nav>
            <a href="panel.php">Panel</a>
            <a href="platillos.php">Volver a platillos</a>
        </nav>
    </header>

        <!-- El formulario se muestra dentro de un contenedor para mantenerlo ordenado. -->
    <main class="formulario-admin">
        <p class="etiqueta">MENÚ</p>
        <h1>Agregar platillo</h1>

        <?php if ($error !== ""): ?>
            <p class="mensaje-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

                <!-- Los datos se envían a este mismo archivo mediante POST. -->
        <!-- Enviar los datos a este mismo archivo para validarlos y guardarlos. -->
        <form method="POST">
            <label>
                Nombre del platillo
                <input type="text" name="nombre" maxlength="120" required>
            </label>

            <label>
                Descripción
                <textarea name="descripcion" rows="4"></textarea>
            </label>

            <!-- El precio admite decimales para poder capturar centavos. -->
            <label>
                Precio (MXN)
                <input type="number" name="precio" min="0" step="0.01" required>
            </label>

                        <!-- El selector muestra las categorías que se consultaron al inicio del archivo. -->
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
    <script src="../assets/js/tema.js" defer></script>
</body>
</html>
