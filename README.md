# Mariscos El Puerto

Sistema web para administrar un restaurante de mariscos. Permite consultar el menú, registrar pedidos por mesa y llevar el control de clientes, proveedores, almacén y facturación.

## Herramientas utilizadas

- PHP para la parte del servidor.
- MySQL para guardar la información.
- HTML para la estructura de las páginas.
- CSS para el diseño.
- JavaScript para calcular el total de los pedidos.
- PDO para conectar PHP con MySQL.

El proyecto está hecho con PHP directo, sin utilizar un framework.

## Cómo instalarlo en XAMPP

1. Copia la carpeta del proyecto en \`C:\\xampp\\htdocs\\CRUD-Mariscos\`.
2. Abre el panel de XAMPP e inicia Apache y MySQL.
3. Entra a phpMyAdmin.
4. Si es la primera instalación, crea la base de datos importando el archivo \`database.sql\`.
5. Si ya tenías una base de datos y solo necesitas agregar las funciones de pedidos, selecciona \`mariscos_db\` e importa \`actualizar_bd.sql\`.
6. Revisa los datos de conexión en \`config/conexion.php\`.
7. Abre \`http://localhost/CRUD-Mariscos/\` para ver el menú.

El archivo \`database.sql\` se utiliza para una instalación desde cero. No lo importes encima de una base de datos que ya tenga información, porque sus tablas se crearán nuevamente y pueden aparecer errores de tablas existentes.

El archivo \`actualizar_bd.sql\` agrega las tablas que necesita el módulo de pedidos sin borrar los registros que ya están guardados.

## Acceso al panel

- Usuario: \`admin\`
- Contraseña: \`mariscos123\`

Son datos de acceso para trabajar en local. Antes de publicar el sistema en internet se necesita mejorar el inicio de sesión y agregar permisos.

## Módulos del sistema

- **Pedidos:** permite elegir una mesa, seleccionar platillos y registrar cantidades.
- **Cobros:** muestra los pedidos pendientes y permite marcarlos como pagados.
- **Menú y precios:** permite agregar, editar, buscar y eliminar platillos.
- **Clientes:** guarda los datos de los clientes.
- **Proveedores:** lleva un registro de los proveedores.
- **Almacén:** guarda productos, existencias y costos.
- **Facturación:** almacena datos fiscales y el estado de las solicitudes.
- **Hacienda / SAT:** representa un posible proceso de conexión, pero funciona únicamente como simulación escolar.

## Base de datos

Las tablas principales son \`clientes\`, \`proveedores\`, \`almacen\` y \`facturacion\`.

También se utilizan tablas para las mesas, categorías, platillos y pedidos. El archivo de instalación incluye registros de ejemplo para poder probar los módulos.

## Sobre la simulación de Hacienda

El apartado de Hacienda no se conecta realmente al SAT. Solo guarda una solicitud de prueba y muestra una respuesta ficticia para representar cómo podría ser el proceso. No genera CFDI ni comprobantes fiscales válidos.

## Organización de carpetas

- \`admin/\`: páginas para administrar el restaurante.
- \`assets/css/\`: estilos del sitio.
- \`config/\`: conexión con la base de datos.
- \`database.sql\`: creación inicial de la base de datos.
- \`actualizar_bd.sql\`: actualización para agregar las tablas de pedidos.
