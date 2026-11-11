<?php
// Iniciar la sesión antes de permitir que se elimine un platillo.
session_start();

// Solo un administrador que inició sesión puede eliminar platillos.
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

// Cargar la conexión con MySQL para ejecutar la eliminación.
require_once "../config/conexion.php";

// Comprobar que la solicitud se haya enviado mediante POST.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Leer el ID enviado por el formulario y validar que sea un entero.
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id) {
        // Preparar la consulta que elimina únicamente el platillo con ese ID.
        $consulta = $conexion->prepare("DELETE FROM platillos WHERE id = ?");
        // Ejecutar el DELETE usando el ID que ya fue validado.
        $consulta->execute([$id]);
    }
}

// Al terminar, regresar al listado para que el usuario vea el resultado.
header("Location: platillos.php?mensaje=" . urlencode("Operación terminada."));
exit;
