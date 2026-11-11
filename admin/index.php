<?php
session_start();

// Acceso inicial para pruebas locales. Cambia estos datos antes de publicar.
$usuarioCorrecto = "admin";
$contrasenaCorrecta = "mariscos123";

if (isset($_POST["entrar"])) {
    $usuario = $_POST["usuario"] ?? "";
    $contrasena = $_POST["contrasena"] ?? "";

    if ($usuario === $usuarioCorrecto && $contrasena === $contrasenaCorrecta) {
        $_SESSION["admin"] = true;
        header("Location: platillos.php");
        exit;
    }

    $error = "Usuario o contraseña incorrectos.";
}

if (isset($_GET["salir"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (!empty($_SESSION["admin"])) {
    header("Location: platillos.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body class="fondo-admin">
    <main class="login">
        <a class="marca" href="../index.php"><span>EL PUERTO</span><small>ADMINISTRACIÓN</small></a>
        <h1>Bienvenido</h1>
        <p>Ingresa para administrar los platillos del menú.</p>
        <?php if (isset($error)): ?><p class="mensaje-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="POST">
            <label>Usuario</label>
            <input type="text" name="usuario" required>
            <label>Contraseña</label>
            <input type="password" name="contrasena" required>
            <button class="boton boton-coral ancho" type="submit" name="entrar">Entrar al panel</button>
        </form>
        <a class="volver" href="../index.php">Volver al sitio</a>
    </main>
</body>
</html>
