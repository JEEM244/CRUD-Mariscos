<?php
// Iniciar o recuperar la sesión del navegador.
session_start();

// Datos de acceso para probar el sistema en local.
// Datos de acceso de prueba para utilizar el panel en la computadora local.
$usuarioCorrecto = "admin";
$contrasenaCorrecta = "mariscos123";

// Comprobar si se presionó el botón Entrar.
if (isset($_POST["entrar"])) {
    // Recibir el usuario y la contraseña escritos en el formulario.
    $usuario = $_POST["usuario"] ?? "";
    $contrasena = $_POST["contrasena"] ?? "";

    // Comparar los datos recibidos con los datos de acceso definidos arriba.
    if ($usuario === $usuarioCorrecto && $contrasena === $contrasenaCorrecta) {
        // Guardar en la sesión que el administrador ya inició sesión.
        $_SESSION["admin"] = true;

        header("Location: panel.php");
        exit;
    }

    $error = "El usuario o la contraseña no son correctos.";
}

// Cerrar la sesión cuando se presiona el enlace de salida.
// Si la dirección contiene salir, cerrar la sesión actual.
if (isset($_GET["salir"])) {
    session_destroy();

    header("Location: index.php");
    exit;
}

// Si ya se inició sesión, no hace falta mostrar el formulario.
// Si ya existe una sesión, llevar al usuario directamente al panel.
if (!empty($_SESSION["admin"])) {
    header("Location: panel.php");
    exit;
}
?>
<!-- Esta parte contiene el formulario de acceso que se muestra en el navegador. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body class="fondo-admin">
        <!-- Contenedor central del formulario de inicio de sesión. -->
    <main class="login">
        <a class="marca" href="../index.php">
            <span>EL PUERTO</span>
            <small>ADMINISTRACIÓN</small>
        </a>

        <h1>Bienvenido</h1>
        <p>Ingresa para administrar los pedidos y la información del restaurante.</p>

        <?php if (isset($error)): ?>
            <p class="mensaje-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

                <!-- Enviar usuario y contraseña a este mismo archivo para validarlos. -->
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
</body>
</html>
