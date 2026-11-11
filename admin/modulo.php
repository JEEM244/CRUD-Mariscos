<?php
session_start();

if (empty($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

require_once "../config/conexion.php";

// Aquí se indica qué información guarda cada módulo.
// Los nombres de las tablas y columnas salen de esta lista.
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

// Se revisa qué módulo se quiere abrir.
$nombreModulo = $_GET["tabla"] ?? "clientes";

if (!isset($modulos[$nombreModulo])) {
    header("Location: panel.php");
    exit;
}

$modulo = $modulos[$nombreModulo];
$titulo = $modulo["titulo"];
$tabla = $modulo["tabla"];
$campos = $modulo["campos"];
$columnas = $modulo["columnas"];
$obligatorios = $modulo["obligatorios"];

$mensaje = "";
$registroEditar = null;

// Guardar cambios o eliminar un registro.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";

    if ($accion === "eliminar") {
        $id = (int) ($_POST["id"] ?? 0);

        try {
            $consulta = $conexion->prepare("DELETE FROM $tabla WHERE id = ?");
            $consulta->execute([$id]);

            $mensaje = "El registro se eliminó correctamente.";
        } catch (PDOException $error) {
            // Puede pasar si otro registro todavía utiliza este dato.
            $mensaje = "No se puede eliminar porque el registro está relacionado con otra información.";
        }
    }

    if ($accion === "guardar") {
        $id = (int) ($_POST["id"] ?? 0);
        $valores = [];

        foreach ($columnas as $columna) {
            $valor = trim($_POST[$columna] ?? "");

            // Los campos numéricos se guardan como números.
            if (in_array($columna, ["cliente_id", "existencia", "costo", "total"], true)) {
                $valor = $valor === "" ? null : $valor;
            }

            $valores[] = $valor;
        }

        // Se revisan los campos que no pueden quedarse vacíos.
        $faltanDatos = false;

        foreach ($obligatorios as $campo) {
            if (trim($_POST[$campo] ?? "") === "") {
                $faltanDatos = true;
                break;
            }
        }

        if ($faltanDatos) {
            $mensaje = "Completa los campos obligatorios.";
        } else {
            if ($id > 0) {
                // Actualizar un registro que ya existe.
                $partes = [];

                foreach ($columnas as $columna) {
                    $partes[] = "$columna = ?";
                }

                $sql = "UPDATE $tabla SET " . implode(", ", $partes) . " WHERE id = ?";
                $consulta = $conexion->prepare($sql);
                $consulta->execute(array_merge($valores, [$id]));

                $mensaje = "Los cambios se guardaron correctamente.";
            } else {
                // Dar de alta un registro nuevo.
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

// Si se eligió editar, se buscan los datos del registro.
if (isset($_GET["editar"])) {
    $idEditar = (int) $_GET["editar"];

    $consulta = $conexion->prepare("SELECT * FROM $tabla WHERE id = ?");
    $consulta->execute([$idEditar]);
    $registroEditar = $consulta->fetch(PDO::FETCH_ASSOC);
}

// Mostrar todos los registros del módulo.
$consulta = $conexion->query("SELECT * FROM $tabla ORDER BY id DESC");
$registros = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> | El Puerto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
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

    <main class="wrap">
        <p class="eyebrow">ADMINISTRACIÓN DE DATOS</p>
        <h1><?= htmlspecialchars($titulo) ?></h1>
        <p class="intro">Aquí puedes agregar, consultar, modificar o eliminar registros.</p>

        <?php if ($mensaje !== ""): ?>
            <p class="notice"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <form class="panel-contenido" method="POST">
            <input type="hidden" name="accion" value="guardar">
            <input type="hidden" name="id" value="<?= $registroEditar["id"] ?? 0 ?>">

            <h2><?= $registroEditar ? "Editar registro" : "Nuevo registro" ?></h2>

            <div class="form-grid">
                <?php foreach ($campos as $campo => $etiqueta): ?>
                    <label>
                        <?= htmlspecialchars($etiqueta) ?>

                        <?php if ($campo === "estado"): ?>
                            <select name="estado">
                                <?php foreach (["Pendiente", "Solicitada", "Emitida"] as $estado): ?>
                                    <option value="<?= $estado ?>"
                                        <?= ($registroEditar["estado"] ?? "Pendiente") === $estado ? "selected" : "" ?>>
                                        <?= $estado ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <?php
                            $tipo = "text";
                            $paso = "1";

                            if (in_array($campo, ["cliente_id", "existencia"], true)) {
                                $tipo = "number";
                                $paso = "1";
                            } elseif (in_array($campo, ["costo", "total"], true)) {
                                $tipo = "number";
                                $paso = "0.01";
                            }
                            ?>

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

        <h2 class="section-title">Registros (<?= count($registros) ?>)</h2>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <?php foreach ($campos as $etiqueta): ?>
                            <th><?= htmlspecialchars($etiqueta) ?></th>
                        <?php endforeach; ?>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($registros as $registro): ?>
                        <tr>
                            <td><?= (int) $registro["id"] ?></td>

                            <?php foreach ($columnas as $columna): ?>
                                <td><?= htmlspecialchars((string) ($registro[$columna] ?? "")) ?></td>
                            <?php endforeach; ?>

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
