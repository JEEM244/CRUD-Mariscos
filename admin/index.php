<?php
// Primero inicio o recupero la sesión del navegador.
session_start();

// Aquí datos de acceso de prueba para utilizar el panel en la computadora local.
$usuarioCorrecto = "admin";
$contrasenaCorrecta = "mariscos123";

// Ahora compruebo si se presionó el botón Entrar.
if (isset($_POST["entrar"])) {
    // Ahora recibo el usuario y la contraseña escritos en el formulario.
    $usuario = $_POST["usuario"] ?? "";
    $contrasena = $_POST["contrasena"] ?? "";

    // Aquí comparo los datos recibidos con los datos de acceso definidos arriba.
    if ($usuario === $usuarioCorrecto && $contrasena === $contrasenaCorrecta) {
        // Ahora guardo en la sesión que el administrador ya inició sesión.
        $_SESSION["admin"] = true;

        header("Location: panel.php");
        exit;
    }

    $error = "El usuario o la contraseña no son correctos.";
}

// Si la dirección contiene salir, cerrar la sesión actual.
if (isset($_GET["salir"])) {
    session_destroy();

    header("Location: index.php");
    exit;
}

// Si ya existe una sesión, llevar al usuario directamente al panel.
if (!empty($_SESSION["admin"])) {
    header("Location: panel.php");
    exit;
}
?>
<!-- Aquí esta parte contiene el formulario de acceso que se muestra en el navegador. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | MARISCOS PIPE</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body class="fondo-admin">
        <!-- Aquí contenedor central del formulario de inicio de sesión. -->
    <main class="login">
        <a class="marca" href="../index.php">
            <span>MARISCOS PIPE</span>
            <small>ADMINISTRACIÓN</small>
        </a>

        <h1>Bienvenido</h1>
        <p>Ingresa para administrar los pedidos y la información del restaurante.</p>

        <?php if (isset($error)): ?>
            <p class="mensaje-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

                <!-- Después envío usuario y contraseña a este mismo archivo para validarlos. -->
        <form method="POST">
            <label>
                Usuario
                <input type="text" name="usuario" autocomplete="username" required>
            </label>

            <label>
                Contraseña
                <input type="password" name="contrasena" autocomplete="current-password" required>
            </label>

            <button class="boton boton-coral ancho" type="submit" name="entrar">
                Entrar al panel
            </button>
        </form>

        <a class="volver" href="../index.php">Volver al menú</a>
    </main>
    <script src="../assets/js/tema.js" defer></script>
</body>
</html>
