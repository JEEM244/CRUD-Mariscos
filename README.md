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

1. Copia la carpeta del proyecto en `C:\xampp\htdocs\CRUD-Mariscos`.
2. Abre el panel de XAMPP e inicia Apache y MySQL.
3. Entra a phpMyAdmin.
4. Si es la primera instalación, crea la base de datos importando el archivo `database.sql`.
5. Si ya tenías una base de datos y solo necesitas agregar las funciones de pedidos, selecciona `mariscos_db` e importa `actualizar_bd.sql`.
6. Revisa los datos de conexión en `config/conexion.php`.
7. Abre `http://localhost:8080/CRUD-Mariscos/` para ver el menú si Apache está configurado en el puerto 8080. Si utilizas el puerto 80, entra a `http://localhost/CRUD-Mariscos/`.

El archivo `database.sql` se utiliza para una instalación desde cero. No lo importes encima de una base de datos que ya tenga información, porque sus tablas se crearán nuevamente y pueden aparecer errores de tablas existentes.

El archivo `actualizar_bd.sql` agrega las tablas que necesita el módulo de pedidos y el campo de imagen de los platillos, sin borrar los registros que ya están guardados. Se puede ejecutar otra vez si necesitas completar la actualización.

## Acceso al panel

- Usuario: `admin`
- Contraseña: `mariscos123`

Son datos de acceso para trabajar en local. Antes de publicar el sistema en internet se necesita mejorar el inicio de sesión y agregar permisos.

## Módulos del sistema

- **Pedidos:** permite elegir una mesa, seleccionar platillos y registrar cantidades.
- **Cobros:** muestra los pedidos pendientes y permite marcarlos como pagados.
- **Menú y precios:** permite agregar, editar, buscar y eliminar platillos.
- **Clientes:** guarda los datos de los clientes.
- **Proveedores:** lleva un registro de los proveedores.
- **Almacén:** guarda productos, existencias y costos.
- **Facturación:** almacena datos fiscales y el estado de las solicitudes.

## Base de datos

Las tablas principales son `clientes`, `proveedores`, `almacen` y `facturacion`.

También se utilizan tablas para las mesas, categorías, platillos y pedidos. El archivo de instalación incluye registros de ejemplo para poder probar los módulos.

## Funciones que podrían agregarse después

La conexión con Hacienda / SAT queda como una idea para una etapa futura del proyecto. Por ahora no existe un módulo de conexión ni se realizan solicitudes a servicios externos. El apartado de facturación se limita a guardar y consultar la información dentro del sistema.

## Organización de carpetas

- `admin/`: páginas para administrar el restaurante.
- `assets/css/`: estilos del sitio.
- `config/`: conexión con la base de datos.
- `database.sql`: creación inicial de la base de datos.
- `actualizar_bd.sql`: actualización para agregar las tablas de pedidos.

## Cómo está explicado el código

Se agregaron comentarios dentro de los archivos para seguir el orden de trabajo:

- En PHP se explica cómo se reciben los datos, se validan, se consultan y se guardan.
- En los formularios HTML se indica para qué sirve cada sección y a qué archivo se envían los datos.
- En JavaScript se explica cómo se agregan o quitan platillos y cómo se calcula el total.
- En CSS se describe qué parte de la página modifica cada grupo de reglas y qué cambia en pantallas pequeñas.
- En SQL se explica para qué sirve cada tabla, qué representa cada campo y cómo se relacionan las tablas.

Los comentarios buscan explicar el motivo de cada paso con palabras sencillas. No cambian lo que hace el programa.
