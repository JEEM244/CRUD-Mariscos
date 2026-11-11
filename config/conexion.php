<?php
// Datos de conexión para XAMPP.
// En una instalación normal de XAMPP, el usuario suele ser root y no tiene contraseña.
// Nombre del servidor donde está funcionando MySQL.
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

    // Mostrar los errores de base de datos mientras se desarrolla el proyecto.
    // Hacer que PDO genere una excepción cuando una consulta tenga un error.
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Si la conexión falla, detener la página y mostrar un mensaje sencillo.
} catch (PDOException $error) {
    // No mostrar los datos internos de la conexión en la página.
    die("No se pudo conectar con la base de datos. Revisa que MySQL esté encendido y que los datos sean correctos.");
}
