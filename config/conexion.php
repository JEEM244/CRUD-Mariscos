<?php
// Aquí datos de conexión para XAMPP. localhost indica que MySQL está funcionando en esta misma computadora.
$servidor = "localhost";
// Aquí usuario de MySQL. En XAMPP suele ser root.
$usuario = "root";
// Aquí contraseña del usuario. En XAMPP normalmente queda vacía al instalarlo.
$contrasena = "";
// Aquí nombre de la base de datos que utiliza este proyecto.
$baseDatos = "mariscos_db";

// Aquí intento abrir la conexión con los datos anteriores.
try {
    // Aquí pDO permite que PHP se comunique con MySQL.
    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    // Aquí configuro PDO para que PDO genere una excepción cuando una consulta tenga un error.
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    // Si la conexión falla, detener la página sin mostrar datos internos de la conexión.
    die("No se pudo conectar con la base de datos. Revisa que MySQL esté encendido y que los datos sean correctos.");
}
