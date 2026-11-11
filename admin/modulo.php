<?php
// Primero inicio la sesión para saber si el administrador ya entró al sistema.
session_start();

// Si no hay una sesión de administrador, lo regreso a la pantalla de acceso.
if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

// Ahora cargo la conexión para poder consultar y guardar información en MySQL.
require_once "../config/conexion.php";

// Primero defino qué tabla utiliza cada módulo, qué campos muestra y cuáles son obligatorios. La lista permite reutilizar el mismo formulario.
$modulos = [
    "clientes" => [
        "titulo" => "Clientes",
        "tabla" => "clientes",
        "campos" => [
            "nombre" => "Nombre",
            "telefono" => "Teléfono",
            "correo" => "Correo",
            "direccion" => "Dirección"
        ],
        "columnas" => ["nombre", "telefono", "correo", "direccion"],
        "obligatorios" => ["nombre"]
    ],
    "proveedores" => [
        "titulo" => "Proveedores",
        "tabla" => "proveedores",
        "campos" => [
            "nombre" => "Nombre",
            "telefono" => "Teléfono",
            "correo" => "Correo",
            "producto" => "Producto que suministra"
        ],
        "columnas" => ["nombre", "telefono", "correo", "producto"],
        "obligatorios" => ["nombre"]
    ],
    "almacen" => [
        "titulo" => "Almacén",
        "tabla" => "almacen",
        "campos" => [
            "producto" => "Producto",
            "categoria" => "Categoría",
            "existencia" => "Existencia",
            "unidad" => "Unidad",
            "costo" => "Costo"
        ],
        "columnas" => ["producto", "categoria", "existencia", "unidad", "costo"],
        "obligatorios" => ["producto", "existencia", "costo"]
    ],
    "facturacion" => [
        "titulo" => "Facturación",
        "tabla" => "facturacion",
        "campos" => [
            "cliente_id" => "ID del cliente",
            "rfc" => "RFC",
            "razon_social" => "Razón social",
            "codigo_postal" => "Código postal",
            "uso_cfdi" => "Uso CFDI",
            "total" => "Total",
            "estado" => "Estado"
        ],
        "columnas" => [
            "cliente_id", "rfc", "razon_social",
            "codigo_postal", "uso_cfdi", "total", "estado"
        ],
        "obligatorios" => [
            "cliente_id", "rfc", "razon_social",
            "codigo_postal", "total"
        ]
    ]
];

// Ahora leo el nombre del módulo que viene en la dirección. Si no viene, abrir Clientes.
$nombreModulo = $_GET["tabla"] ?? "clientes";

// Ahora compruebo que el módulo solicitado esté dentro de la lista permitida.
if (!isset($modulos[$nombreModulo])) {
    header("Location: panel.php");
    exit;
}

// Aquí guardo en variables los datos del módulo que se va a trabajar.
$modulo = $modulos[$nombreModulo];
$titulo = $modulo["titulo"];
$tabla = $modulo["tabla"];
$campos = $modulo["campos"];
$columnas = $modulo["columnas"];
$obligatorios = $modulo["obligatorios"];

$mensaje = "";
$registroEditar = null;

// Ahora reviso si el formulario se envió. Después se decide si se va a guardar o eliminar un registro.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";

    // Si se pidió eliminar, identificar el registro y borrarlo de la tabla.
    if ($accion === "eliminar") {
        $id = (int) ($_POST["id"] ?? 0);

        // Ahora intento eliminar el registro. Si está relacionado con otra tabla, MySQL puede impedirlo.
        try {
            $consulta = $conexion->prepare("DELETE FROM $tabla WHERE id = ?");
            $consulta->execute([$id]);

            $mensaje = "El registro se eliminó correctamente.";
        } catch (PDOException $error) {
            // Esto puede pasar si otro registro todavía utiliza este dato.
            $mensaje = "No se puede eliminar porque el registro está relacionado con otra información.";
        }
    }

    // Si se pidió guardar, revisar si se trata de un registro nuevo o de una edición.
    if ($accion === "guardar") {
        $id = (int) ($_POST["id"] ?? 0);
        $valores = [];

        // Ahora recorro los campos del módulo y tomar los valores que escribió el usuario.
        foreach ($columnas as $columna) {
            $valor = trim($_POST[$columna] ?? "");

            // En este punto, los campos numéricos pueden quedar como null cuando no se escribió ningún valor.
            if (in_array($columna, ["cliente_id", "existencia", "costo", "total"], true)) {
                $valor = $valor === "" ? null : $valor;
            }

            $valores[] = $valor;
        }

        // Primero empiezo suponiendo que no faltan datos y revisar los campos obligatorios del módulo.
        $faltanDatos = false;
        foreach ($obligatorios as $campo) {
            // Si un campo obligatorio está vacío, indicar que faltan datos.
            if (trim($_POST[$campo] ?? "") === "") {
                $faltanDatos = true;
                break;
            }
        }

        // Aquí no guardar el registro mientras falte alguno de los datos obligatorios.
        if ($faltanDatos) {
            $mensaje = "Completa los campos obligatorios.";
        } else {
            // Si el ID es mayor que cero, el registro ya existe y se deben guardar sus cambios.
            if ($id > 0) {
                $partes = [];

                // Ahora preparo una condición por cada columna que se va a actualizar.
                foreach ($columnas as $columna) {
                    $partes[] = "$columna = ?";
                }

                // Aquí preparo la consulta UPDATE con los campos permitidos para este módulo.
                $sql = "UPDATE $tabla SET " . implode(", ", $partes) . " WHERE id = ?";
                $consulta = $conexion->prepare($sql);
                $consulta->execute(array_merge($valores, [$id]));

                $mensaje = "Los cambios se guardaron correctamente.";
            } else {
                // Si no hay ID, se trata de un registro nuevo y se utiliza INSERT.
                $signos = implode(", ", array_fill(0, count($columnas), "?"));
                $listaColumnas = implode(", ", $columnas);

                $sql = "INSERT INTO $tabla ($listaColumnas) VALUES ($signos)";
                $consulta = $conexion->prepare($sql);
                $consulta->execute($valores);

                $mensaje = "El registro se agregó correctamente.";
            }

            // Después de guardar, el formulario vuelve a quedar vacío.
            $registroEditar = null;
        }
    }
}

// Ahora reviso si la dirección contiene un ID para cargar ese registro en el formulario.
if (isset($_GET["editar"])) {
    $idEditar = (int) $_GET["editar"];

    $consulta = $conexion->prepare("SELECT * FROM $tabla WHERE id = ?");
    $consulta->execute([$idEditar]);
    $registroEditar = $consulta->fetch(PDO::FETCH_ASSOC);
}

// Ahora consulto los registros del módulo para mostrarlos en la tabla.
$consulta = $conexion->query("SELECT * FROM $tabla ORDER BY id DESC");
$registros = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Aquí a partir de aquí comienza la parte HTML que verá el administrador. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <!-- Aquí barra superior: muestra el nombre del sistema y los enlaces de navegación. -->
    <header class="topbar">
        <a class="logo-admin" href="panel.php">
            EL PUERTO
            <small>ADMINISTRACIÓN</small>
        </a>

        <nav>
            <a href="panel.php">Panel</a>
            <a href="index.php?salir=1">Cerrar sesión</a>
        </nav>
    </header>

    <!-- Aquí área principal donde se muestra el formulario y la lista de registros. -->
    <main class="wrap">
        <p class="eyebrow">ADMINISTRACIÓN DE DATOS</p>
        <h1><?= htmlspecialchars($titulo) ?></h1>
        <p class="intro">Aquí puedes agregar, consultar, modificar o eliminar registros.</p>

        <?php if ($mensaje !== ""): ?>
            <p class="notice"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <!-- Aquí este formulario sirve tanto para agregar un registro como para editarlo. -->
        <form class="panel-contenido" method="POST">
            <!-- Aquí estos campos ocultos indican que se va a guardar y cuál registro se está editando. -->
            <input type="hidden" name="accion" value="guardar">
            <input type="hidden" name="id" value="<?= $registroEditar["id"] ?? 0 ?>">

            <h2><?= $registroEditar ? "Editar registro" : "Nuevo registro" ?></h2>

            <!-- Aquí el formulario coloca sus campos en una cuadrícula de dos columnas. -->
            <div class="form-grid">
                <!-- Ahora recorro los campos definidos para mostrar la etiqueta y su control. -->
                <?php foreach ($campos as $campo => $etiqueta): ?>
                    <label>
                        <?= htmlspecialchars($etiqueta) ?>

                        <!-- Aquí el estado usa una lista de opciones para evitar escribir valores distintos. -->
                        <?php if ($campo === "estado"): ?>
                            <select name="estado">
                                <!-- Ahora recorro los estados permitidos y crear una opción por cada uno. -->
                                <?php foreach (["Pendiente", "Solicitada", "Emitida"] as $estado): ?>
                                    <option value="<?= $estado ?>"
                                        <?= ($registroEditar["estado"] ?? "Pendiente") === $estado ? "selected" : "" ?>>
                                        <?= $estado ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                        <?php
                            // Ahora elijo el tipo de campo. La propiedad step indica si se permiten enteros o cantidades con decimales.
                            $tipo = "text";
                            $paso = "1";

                            // En este caso, los IDs y las existencias son números enteros.
                            if (in_array($campo, ["cliente_id", "existencia"], true)) {
                                $tipo = "number";
                                $paso = "1";
                            } elseif (in_array($campo, ["costo", "total"], true)) {
                                $tipo = "number";
                                $paso = "0.01";
                            }
                            ?>

                            <!-- Aquí muestro el campo con su tipo, valor actual y validaciones básicas. -->
                            <input
                                type="<?= $tipo ?>"
                                name="<?= $campo ?>"
                                value="<?= htmlspecialchars((string) ($registroEditar[$campo] ?? "")) ?>"
                                step="<?= $paso ?>"
                                <?= in_array($campo, $obligatorios, true) ? "required" : "" ?>
                                <?= $campo === "cliente_id" ? 'min="1"' : "" ?>
                                <?= $campo === "existencia" || $campo === "costo" || $campo === "total" ? 'min="0"' : "" ?>
                                <?= $campo === "rfc" ? 'maxlength="13"' : "" ?>
                                <?= $campo === "codigo_postal" ? 'maxlength="5"' : "" ?>
                            >
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <button class="boton-principal" type="submit">
                <?= $registroEditar ? "Guardar cambios" : "Agregar registro" ?>
            </button>

            <?php if ($registroEditar): ?>
                <a class="text-link" href="modulo.php?tabla=<?= urlencode($nombreModulo) ?>">Cancelar edición</a>
            <?php endif; ?>
        </form>

        <!-- Aquí tabla que permite consultar todos los registros guardados. -->
        <h2 class="section-title">Registros (<?= count($registros) ?>)</h2>

        <div class="table-box">
            <table>
                <!-- Aquí encabezados de las columnas de la tabla. -->
                <thead>
                    <tr>
                        <th>ID</th>
                                <!-- Aquí crear un encabezado por cada campo del módulo. -->
                        <?php foreach ($campos as $etiqueta): ?>
                            <th><?= htmlspecialchars($etiqueta) ?></th>
                        <?php endforeach; ?>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <!-- Aquí filas con los datos que llegaron desde MySQL. -->
                <tbody>
                    <!-- Aquí mostrar una fila por cada registro que se encontró en la base de datos. -->
                    <?php foreach ($registros as $registro): ?>
                        <tr>
                            <td><?= (int) $registro["id"] ?></td>

                            <!-- Aquí muestro el valor de cada columna en la fila actual. -->
                            <?php foreach ($columnas as $columna): ?>
                                <td><?= htmlspecialchars((string) ($registro[$columna] ?? "")) ?></td>
                            <?php endforeach; ?>

                            <!-- Aquí en cada fila se ofrecen las acciones de editar y eliminar. -->
                            <td class="acciones">
                                <a href="modulo.php?tabla=<?= urlencode($nombreModulo) ?>&editar=<?= (int) $registro["id"] ?>">
                                    Editar
                                </a>

                                <form
                                    method="POST"
                                    class="inline-form"
                                    onsubmit="return confirm('¿Seguro que quieres eliminar este registro?')"
                                >
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?= (int) $registro["id"] ?>">
                                    <button class="danger-link" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($registros) === 0): ?>
                        <tr>
                            <td colspan="<?= count($campos) + 2 ?>">Todavía no hay registros.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
