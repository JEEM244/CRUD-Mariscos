<?php
session_start();
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}
require_once "../config/conexion.php";

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = $_POST["precio"] ?? "";
    $imagen = trim($_POST["imagen"] ?? "");
    $categoria = $_POST["categoria"] ?? "";

    if ($nombre === "" || !is_numeric($precio) || $precio < 0) {
        $error = "Escribe un nombre y un precio válido.";
    } else {
        $sql = "INSERT INTO platillos (nombre, descripcion, precio, imagen, categoria_id, disponible)
                VALUES (:nombre, :descripcion, :precio, :imagen, :categoria, 1)";
        $guardar = $conexion->prepare($sql);
        $guardar->execute([
            ":nombre" => $nombre,
            ":descripcion" => $descripcion,
            ":precio" => $precio,
            ":imagen" => $imagen,
            ":categoria" => $categoria !== "" ? $categoria : null
        ]);
        header("Location: platillos.php?mensaje=Platillo%20agregado");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Nuevo platillo</title><link rel="stylesheet" href="../assets/css/estilos.css"></head>
<body class="admin-pagina">
<header class="barra barra-admin"><a class="marca" href="../index.php"><span>EL PUERTO</span><small>ADMINISTRACIÓN</small></a><nav><a href="platillos.php">Volver a platillos</a></nav></header>
<main class="formulario-admin">
    <p class="etiqueta">MENÚ</p><h1>Agregar platillo</h1>
    <?php if ($error !== ""): ?><p class="mensaje-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        <label>Nombre del platillo</label><input type="text" name="nombre" maxlength="120" required>
        <label>Descripción</label><textarea name="descripcion" rows="4"></textarea>
        <label>Precio (MXN)</label><input type="number" name="precio" min="0" step="0.01" required>
        <label>Categoría</label><select name="categoria"><option value="">Sin categoría</option><?php foreach ($categorias as $categoria): ?><option value="<?= $categoria["id"] ?>"><?= htmlspecialchars($categoria["nombre"]) ?></option><?php endforeach; ?></select>
        <label>URL de imagen (opcional)</label><input type="url" name="imagen" placeholder="https://ejemplo.com/platillo.jpg">
        <button class="boton boton-coral" type="submit">Guardar platillo</button>
        <a class="volver" href="platillos.php">Cancelar</a>
    </form>
</main>
</body></html>
