<?php
require_once "config/conexion.php";

// Obtener los platillos que están disponibles para mostrarse en el menú.
$sql = "SELECT
            platillos.*,
            categorias.nombre AS nombre_categoria
        FROM platillos
        LEFT JOIN categorias ON platillos.categoria_id = categorias.id
        WHERE platillos.disponible = 1
        ORDER BY platillos.id DESC";

$consulta = $conexion->query($sql);
$platillos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mariscos El Puerto</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <header class="barra">
        <a class="marca" href="index.php">
            <span>EL PUERTO</span>
            <small>MARISCOS</small>
        </a>

        <nav>
            <a href="#menu">Nuestro menú</a>
            <a href="#contacto">Contacto</a>
            <a class="boton boton-claro" href="admin/">Administrar</a>
        </nav>
    </header>

    <main>
        <section class="portada">
            <div class="portada-contenido">
                <p class="etiqueta">FRESCO DEL MAR A TU MESA</p>
                <h1>El sabor del mar<br><em>está aquí.</em></h1>
                <p>
                    Disfruta mariscos preparados al momento, con ingredientes frescos
                    y el sabor que te hace volver.
                </p>
                <a class="boton boton-coral" href="#menu">Ver menú</a>
            </div>

            <div class="portada-imagen">
                <div class="circulo">SABOR<br>FRESCO</div>
            </div>
        </section>

        <section class="seccion-menu" id="menu">
            <div class="encabezado-seccion">
                <div>
                    <p class="etiqueta">DIRECTO A TU MESA</p>
                    <h2>Nuestro menú</h2>
                </div>

                <p>Una selección de nuestros platillos favoritos.</p>
            </div>

            <div class="tarjetas">
                <?php foreach ($platillos as $platillo): ?>
                    <article class="tarjeta">
                        <div class="tarjeta-imagen">
                            <?php if (!empty($platillo["imagen"])): ?>
                                <img
                                    src="<?= htmlspecialchars($platillo["imagen"]) ?>"
                                    alt="<?= htmlspecialchars($platillo["nombre"]) ?>"
                                >
                            <?php else: ?>
                                <span>Sin imagen</span>
                            <?php endif; ?>
                        </div>

                        <div class="tarjeta-info">
                            <small>
                                <?= htmlspecialchars($platillo["nombre_categoria"] ?? "Especialidad") ?>
                            </small>

                            <h3><?= htmlspecialchars($platillo["nombre"]) ?></h3>

                            <p><?= htmlspecialchars($platillo["descripcion"] ?? "") ?></p>

                            <strong>
                                $<?= number_format((float) $platillo["precio"], 2) ?> MXN
                            </strong>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if (count($platillos) === 0): ?>
                    <p>Por el momento no hay platillos disponibles.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="franja" id="contacto">
            <div>
                <p class="etiqueta">MARISCOS EL PUERTO</p>
                <h2>Un buen día comienza con algo del mar.</h2>
            </div>

            <p>Consulta nuestro menú y descubre tu próximo platillo favorito.</p>
        </section>
    </main>

    <footer>
        <span>© <?= date("Y") ?> Mariscos El Puerto</span>
        <a href="admin/">Panel administrativo</a>
    </footer>
</body>
</html>
