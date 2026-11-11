<?php
// Datos de conexión para XAMPP.
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "mariscos_db";

try {
    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$base_datos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    die("No se pudo conectar con la base de datos. Revisa config/conexion.php.");
}
?>
