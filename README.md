# MARISCOS PIPE

Sistema web para la administración de un restaurante de mariscos. Está pensado para facilitar el trabajo diario: consultar el menú, registrar pedidos por mesa, realizar cobros y mantener organizados los datos del restaurante.

El proyecto utiliza PHP y MySQL. Su interfaz está hecha con HTML, CSS y JavaScript, sin depender de frameworks de diseño.

## Características principales

- Registro de pedidos relacionados con una mesa.
- Selección de platillos y cantidades.
- Cálculo del total de cada pedido.
- Consulta de pedidos pendientes y registro de cobros.
- Administración de platillos, categorías y disponibilidad.
- Registro de clientes y proveedores.
- Control de productos y existencias del almacén.
- Registro y consulta de información de facturación.

## Tecnologías utilizadas

- **PHP:** procesa formularios y realiza las operaciones del sistema.
- **MySQL:** almacena los datos del restaurante.
- **PDO:** permite conectar PHP con MySQL mediante consultas preparadas.
- **HTML:** organiza el contenido de las páginas.
- **CSS:** define la distribución, los colores y la adaptación a diferentes pantallas.
- **JavaScript:** permite realizar acciones en la interfaz y calcular importes.

El proyecto está desarrollado con PHP directo, sin utilizar un framework de backend.

## Diseño de la interfaz

La identidad visual del sistema se llama **MARISCOS PIPE**. Su diseño toma como referencia el minimalismo editorial japonés, pero no pretende representar un restaurante japonés.

La interfaz busca ser clara, ordenada y profesional. Para lograrlo utiliza:

- Una paleta de colores neutros y cálidos.
- Tipografía serif para algunos títulos y sans-serif para los datos y controles.
- Espacios y líneas divisorias para separar la información.
- Una navegación lateral en el panel administrativo.
- Distribuciones diferentes según la función de cada sección, en lugar de repetir tarjetas en todas las páginas.
- Tema claro y tema oscuro. La preferencia se guarda en el navegador.

Los estilos están escritos en CSS propio. No se utiliza Bootstrap ni W3.CSS. La documentación de W3Schools puede servir como referencia para las propiedades y conceptos de CSS, pero el diseño del proyecto se programa directamente en sus archivos.

## Requisitos

Para ejecutar el proyecto en local se necesita:

- XAMPP.
- Apache.
- MySQL o MariaDB.
- Un navegador web.

## Instalación en XAMPP

1. Copia la carpeta del proyecto en:

   `C:\xampp\htdocs\CRUD-Mariscos`

2. Abre el panel de XAMPP e inicia Apache y MySQL.

3. Entra a phpMyAdmin:

   `http://localhost:8080/phpmyadmin`

   Si Apache utiliza otro puerto, ajusta la dirección.

4. Para una instalación desde cero, importa el archivo `database.sql`. Este archivo crea la estructura inicial de la base de datos e incluye registros de ejemplo.

5. Si ya tienes una base de datos del proyecto y necesitas aplicar cambios, selecciona `mariscos_db` e importa `actualizar_bd.sql`.

6. Revisa la configuración de conexión en `config/conexion.php`.

7. Abre el sistema en el navegador:

   `http://localhost:8080/CRUD-Mariscos/`

   Si Apache utiliza el puerto 80, la dirección será:

   `http://localhost/CRUD-Mariscos/`

### Importante sobre los archivos SQL

- **`database.sql`:** se utiliza para crear la base de datos desde cero. No lo importes encima de una instalación que ya contiene información, porque puede ocasionar conflictos con las tablas existentes.
- **`actualizar_bd.sql`:** sirve para aplicar cambios a una instalación existente sin tener que reinstalar todo el proyecto. Revisa el contenido del archivo antes de ejecutarlo y conserva una copia de seguridad de tu base de datos.

## Acceso al panel administrativo

En la configuración de demostración local:

- **Usuario:** `admin`
- **Contraseña:** `mariscos123`

Estas credenciales son únicamente para pruebas locales. No deben conservarse al publicar el sistema en internet. Antes de utilizarlo en producción se deben implementar medidas adicionales de seguridad, permisos y protección de cuentas.

## Módulos

| Módulo | Función |
|---|---|
| Pedidos | Seleccionar una mesa, agregar platillos y registrar cantidades. |
| Cobros | Consultar pedidos pendientes y registrar su pago. |
| Platillos | Agregar, editar, buscar y eliminar platillos. |
| Clientes | Guardar y consultar información de clientes. |
| Proveedores | Mantener los datos de los proveedores. |
| Almacén | Registrar productos, existencias y costos. |
| Facturación | Guardar información fiscal y consultar solicitudes. |

## Base de datos

La base de datos utilizada por el proyecto se llama `mariscos_db`.

Entre sus tablas se encuentran:

- `clientes`
- `proveedores`
- `almacen`
- `facturacion`
- `mesas`
- `categorias`
- `platillos`
- `pedidos`
- `detalle_pedido`

Las tablas se relacionan para asociar los pedidos con sus mesas, los detalles con sus platillos y los registros administrativos con la información correspondiente.

## Facturación

El módulo de facturación permite guardar y consultar información dentro del sistema. Actualmente no existe una conexión con el SAT ni se generan comprobantes fiscales digitales. Una integración con servicios externos sería una función para una etapa futura.

## Organización del proyecto

- `admin/`: páginas del panel administrativo y sus módulos.
- `assets/css/`: hojas de estilo de la interfaz.
- `assets/js/`: funciones JavaScript, incluido el selector de tema.
- `config/`: configuración de la conexión con la base de datos.
- `database.sql`: instalación inicial de la base de datos.
- `actualizar_bd.sql`: actualización de la estructura y datos de una instalación existente.

## Comentarios y explicación del código

El código contiene comentarios para explicar qué hace cada parte y facilitar su lectura:

- **PHP:** recepción y validación de datos, consultas y operaciones de registro.
- **HTML:** estructura de los formularios, tablas y secciones.
- **CSS:** distribución de los elementos, colores, tipografía, estados de los controles y ajustes para pantallas pequeñas.
- **JavaScript:** acciones de la interfaz y cálculo de los importes.
- **SQL:** creación de tablas, campos, relaciones y registros de ejemplo.

Los comentarios están escritos con palabras sencillas y sirven como guía para entender el funcionamiento del programa.
