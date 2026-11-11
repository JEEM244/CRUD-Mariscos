<?php
// Datos de conexión para XAMPP.
// En una instalación normal de XAMPP, el usuario suele ser root y no tiene contraseña.
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$baseDatos = "mariscos_db";

try {
    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    // Mostrar los errores de base de datos mientras se desarrolla el proyecto.
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    // No mostrar los datos internos de la conexión en la página.
    die("No se pudo conectar con la base de datos. Revisa que MySQL esté encendido y que los datos sean correctos.");
}
