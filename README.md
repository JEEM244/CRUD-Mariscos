# Mariscos El Puerto

Sistema web para una marisquería con catálogo público y panel de administración CRUD.

## Tecnologías
- PHP 8+
- MySQL / MariaDB
- HTML5 y CSS3

## Instalación con XAMPP
1. Copia el proyecto dentro de `C:\\xampp\\htdocs\\CRUD-Mariscos`.
2. Inicia Apache y MySQL desde XAMPP.
3. Abre phpMyAdmin en `http://localhost/phpmyadmin`.
4. Importa el archivo `database.sql`.
5. Revisa los datos de conexión en `config/conexion.php`.
6. Abre `http://localhost/CRUD-Mariscos/`.

## Panel de administración
- Dirección: `http://localhost/CRUD-Mariscos/admin/`
- Usuario: `admin`
- Contraseña inicial: `mariscos123`

Cambia la contraseña antes de publicar el sitio en internet. Este acceso inicial es para desarrollo local.

## Funciones
- Consultar platillos en el menú público.
- Crear, editar y eliminar platillos.
- Activar o desactivar platillos.
- Buscar platillos por nombre.
- Registrar nombre, descripción, categoría, precio e imagen (URL).

## Estructura
- `config/`: conexión a la base de datos.
- `admin/`: inicio de sesión y administración.
- `assets/css/`: estilos.
- `database.sql`: base de datos y datos de ejemplo.
