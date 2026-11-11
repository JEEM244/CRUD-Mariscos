<?php
// Cargar la conexión para poder leer los platillos guardados en MySQL.
require_once "config/conexion.php";

// Consultar los platillos disponibles y el nombre de la categoría a la que pertenecen.
$sql = "SELECT
            platillos.*,
            categorias.nombre AS nombre_categoria
        FROM platillos
        LEFT JOIN categorias ON platillos.categoria_id = categorias.id
        WHERE platillos.disponible = 1
        ORDER BY platillos.id DESC";

// Ejecutar la consulta que se preparó arriba.
$consulta = $conexion->query($sql);
// Guardar todos los resultados en un arreglo para recorrerlos en el HTML.
$platillos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Esta es la página pública que pueden visitar los clientes del restaurante. -->
<!DOCTYPE html>
<html lang="es">
    <!-- Configuración del documento: idioma, tamaño de pantalla, título y CSS. -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MARISCOS PIPE</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body class="public-site">
        <!-- Encabezado público con el nombre del restaurante y los enlaces del menú. -->
    <header class="barra">
        <a class="marca" href="index.php">
            <span>MARISCOS PIPE</span>
            <small>MARISCOS</small>
        </a>

        <nav>
            <a href="#menu">Nuestro menú</a>
            <a href="#contacto">Contacto</a>
            <a href="#creditos">Créditos</a>
            <a class="boton boton-claro" href="admin/">Administrar</a>
        </nav>
    </header>

        <!-- Contenido que verán los visitantes del restaurante. -->
    <main>
                <!-- Presentación principal del restaurante. -->
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

                <!-- Sección donde se muestran los platillos obtenidos desde MySQL. -->
        <section class="seccion-menu" id="menu">
            <div class="encabezado-seccion">
                <div>
                    <p class="etiqueta">DIRECTO A TU MESA</p>
                    <h2>Nuestro menú</h2>
                </div>

                <p>Una selección de nuestros platillos favoritos.</p>
            </div>

                        <!-- Crear una tarjeta por cada platillo disponible. -->
            <div class="tarjetas">
                                    <!-- Recorrer los platillos para mostrar su imagen, descripción y precio. -->
                    <?php foreach ($platillos as $platillo): ?>
                    <article class="tarjeta">
                        <div class="tarjeta-imagen">
                                                        <!-- Si el platillo tiene una imagen guardada, mostrarla. -->
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

                <!-- Sección final con información breve del restaurante. -->
        <section class="franja" id="contacto">
            <div>
                <p class="etiqueta">MARISCOS PIPE</p>
                <h2>Un buen día comienza con algo del mar.</h2>
            </div>

            <p>Consulta nuestro menú y descubre tu próximo platillo favorito.</p>
        </section>

        <!-- Agradecimientos a las personas y recursos que ayudaron durante el desarrollo. -->
        <section class="creditos" id="creditos">
            <div class="creditos-intro">
                <p class="etiqueta">DETRÁS DEL PROYECTO</p>
                <h2>Créditos y agradecimientos</h2>
                <p>
                    Este proyecto también es resultado de experiencias, aprendizajes
                    y recursos que me ayudaron a llevarlo a cabo.
                </p>
            </div>

            <div class="creditos-lista">
                <article>
                    <span>01</span>
                    <div>
                        <h3>Mi anterior servicio social</h3>
                        <p>
                            Agradezco a las personas que formaron parte de mi anterior
                            servicio social. Aunque fue una experiencia difícil y hubo
                            momentos complicados, me permitió conocer cómo funciona un
                            negocio desde dentro y comprender mejor sus procesos, desde
                            el control del almacén hasta la organización de las actividades
                            de una empresa. Ese aprendizaje fue una de las bases para pensar
                            y desarrollar este sistema.
                        </p>
                    </div>
                </article>

                <article>
                    <span>02</span>
                    <div>
                        <h3>W3Schools</h3>
                        <p>
                            Gracias a W3Schools por sus ejemplos y explicaciones, que
                            utilicé como referencia para aprender y trabajar con CSS
                            durante el diseño de la página.
                        </p>
                    </div>
                </article>

                <article>
                    <span>03</span>
                    <div>
                        <h3>Creadores de contenido en YouTube</h3>
                        <p>
                            Agradezco a los creadores de videos y tutoriales de YouTube
                            que compartieron sus conocimientos y me ayudaron a resolver
                            dudas durante la programación del CRUD y el desarrollo de
                            sus diferentes funciones.
                        </p>
                    </div>
                </article>
            </div>
        </section>
    </main>

        <!-- Pie de página con el año actual y el acceso al panel administrativo. -->
    <footer>
        <span>© <?= date("Y") ?> MARISCOS PIPE</span>
        <a href="admin/">Panel administrativo</a>
    </footer>
    <script src="assets/js/tema.js" defer></script>
</body>
</html>
