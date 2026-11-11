# Mariscos El Puerto — Sistema de pedidos

Sistema web de restaurante para tomar pedidos por mesa, consultar el menú y administrar clientes, proveedores, facturación y almacén.

## Tecnologías
- PHP básico a intermedio (sin framework)
- MySQL / MariaDB
- HTML, CSS y JavaScript básico
- PDO para conexión y consultas preparadas

## Instalación local con XAMPP
1. Copia la carpeta en `C:\\xampp\\htdocs\\CRUD-Mariscos`.
2. Inicia Apache y MySQL.
3. Abre phpMyAdmin y ejecuta/importa `database.sql`.
4. Revisa `config/conexion.php` si tu usuario o contraseña de MySQL son diferentes.
5. Abre `http://localhost/CRUD-Mariscos/admin/`.

## Acceso local
- Usuario: `admin`
- Contraseña: `mariscos123`

Es una cuenta de prueba para desarrollo local. Antes de publicar el sistema se debe implementar autenticación con contraseñas cifradas, roles y protección CSRF.

## Módulos
- **Pedidos:** elegir mesa, cliente opcional, platillos y cantidades. El total se calcula y guarda.
- **Clientes:** alta, consulta, actualización y eliminación.
- **Proveedores:** alta, consulta, actualización y eliminación.
- **Facturación:** captura de datos fiscales y estado de la factura.
- **Almacén:** control básico de existencias y costos.
- **Menú:** administración de platillos y precios.

## Base de datos
Las cuatro tablas principales son `clientes`, `proveedores`, `facturacion` y `almacen`. Se agregan tablas auxiliares para mesas, categorías, platillos, pedidos y detalles del pedido. Hay al menos 10 registros de ejemplo en cada tabla principal.

## Facturación electrónica
El módulo almacena datos fiscales y permite llevar un estado de seguimiento. **No emite CFDI ni está conectado al SAT todavía.** Para eso se necesita integrar un PAC autorizado, configurar credenciales y realizar pruebas conforme a los requisitos fiscales vigentes.

## Nota de desarrollo
El diseño usa una apariencia retro de videojuego/arcade sencilla: fondo oscuro, paneles tipo pixel, bordes marcados y acentos turquesa, rosa y amarillo. El código se mantiene en PHP directo para que sea entendible en un nivel básico-intermedio.
