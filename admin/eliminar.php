<?php
session_start();

if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

require_once "../config/conexion.php";

// Este archivo solo recibe la petición de eliminar desde platillos.php.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id) {
        $consulta = $conexion->prepare("DELETE FROM platillos WHERE id = ?");
        $consulta->execute([$id]);
    }
}

header("Location: platillos.php?mensaje=" . urlencode("Operación terminada."));
exit;
