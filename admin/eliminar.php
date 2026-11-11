<?php
session_start();
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id) {
        $eliminar = $conexion->prepare("DELETE FROM platillos WHERE id = :id");
        $eliminar->execute([":id" => $id]);
    }
}

header("Location: platillos.php?mensaje=Platillo%20eliminado");
exit;
?>
