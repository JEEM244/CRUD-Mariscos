<?php
// Datos de conexión para XAMPP. localhost indica que MySQL está funcionando en esta misma computadora.
$servidor = "localhost";
// Usuario de MySQL. En XAMPP suele ser root.
$usuario = "root";
// Contraseña del usuario. En XAMPP normalmente queda vacía al instalarlo.
$contrasena = "";
// Nombre de la base de datos que utiliza este proyecto.
$baseDatos = "mariscos_db";

// Intentar abrir la conexión con los datos anteriores.
try {
    // PDO permite que PHP se comunique con MySQL.
    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    // Hacer que PDO genere una excepción cuando una consulta tenga un error.
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    // Si la conexión falla, detener la página sin mostrar datos internos de la conexión.
    die("No se pudo conectar con la base de datos. Revisa que MySQL esté encendido y que los datos sean correctos.");
}
