# Restaurant Management System

Sistema de gestión de restaurante desarrollado en PHP para fines educativos. Este proyecto demuestra la implementación de una API REST y un sitio web utilizando arquitectura MVC.

## Características

- **API REST** completa con CRUD para todas las entidades
- **Autenticación** mediante tokens simétricos
- **Sitio web MVC** con área pública y panel de administración
- **Carrito de compras** usando localStorage
- **Documentación API** con apidoc
- **Bootstrap 5** para el diseño responsive

## Requisitos

- PHP 7.4 o superior
- MariaDB/MySQL
- Servidor web (Apache/Nginx)
- Node.js y npm (para generar documentación API)

## Instalación

### 1. Clonar/Descargar el proyecto

```bash
cd /ruta/del/proyecto
```

### 2. Configurar la base de datos

1. Crear la base de datos ejecutando el script SQL:
```bash
mysql -u root -p < sql/schema.sql
```

2. Configurar las credenciales de la base de datos en `config/database.php`:
```php
private $host = 'localhost';
private $dbname = 'restaurant_db';
private $username = 'tu_usuario';
private $password = 'tu_contraseña';
```

### 3. Configurar la aplicación

Editar `config/config.php` y ajustar:
- `APP_URL` con la URL base de tu proyecto
- `API_TOKEN_SECRET` con una clave secreta segura

### 4. Configurar el servidor web

#### Apache (.htaccess)
Crear un archivo `.htaccess` en `public/`:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 5. Instalar dependencias para documentación (opcional)

```bash
npm install -g apidoc
```

## Uso

### Generar documentación de la API

```bash
apidoc -i public/api -o docs
```

La documentación se generará en la carpeta `docs/`.

### Acceder al sitio

- **Sitio web**: `http://localhost/restaurant/public/`
- **API**: `http://localhost/restaurant/public/api/`
- **Documentación API**: `http://localhost/restaurant/public/docs/` (después de generarla)

### Usuario por defecto

Después de ejecutar el script SQL, puedes iniciar sesión con:
- **Email**: admin@restaurant.com
- **Contraseña**: admin123

## Estructura del Proyecto

```
restaurant/
├── config/              # Configuración
│   ├── config.php
│   └── database.php
├── public/              # Punto de entrada público
│   ├── index.php        # Router principal
│   ├── api/            # Endpoints de la API
│   └── assets/         # CSS, JS, imágenes
├── src/
│   ├── controllers/    # Controladores MVC
│   ├── models/         # Modelos de datos
│   ├── views/          # Vistas/templates
│   ├── middleware/     # Middleware (autenticación)
│   └── utils/          # Utilidades
├── sql/                # Scripts SQL
│   └── schema.sql
└── docs/               # Documentación API (generada)
```

## API Endpoints

### Autenticación
- `POST /api/auth/login` - Iniciar sesión

### Usuarios
- `GET /api/user/` - Listar usuarios (requiere admin)
- `POST /api/user/` - Crear usuario (requiere admin)
- `GET /api/user/{id}` - Obtener usuario
- `PUT /api/user/{id}` - Actualizar usuario
- `DELETE /api/user/{id}` - Eliminar usuario (requiere admin)

### Categorías
- `GET /api/category/` - Listar categorías
- `POST /api/category/` - Crear categoría (requiere admin)
- `GET /api/category/{id}` - Obtener categoría
- `PUT /api/category/{id}` - Actualizar categoría (requiere admin)
- `DELETE /api/category/{id}` - Eliminar categoría (requiere admin)

### Productos
- `GET /api/product/` - Listar productos
- `POST /api/product/` - Crear producto (requiere admin)
- `GET /api/product/{id}` - Obtener producto
- `PUT /api/product/{id}` - Actualizar producto (requiere admin)
- `DELETE /api/product/{id}` - Eliminar producto (requiere admin)

### Promociones
- `GET /api/promotion/` - Listar promociones
- `POST /api/promotion/validate` - Validar código de promoción
- `POST /api/promotion/` - Crear promoción (requiere admin)
- `GET /api/promotion/{id}` - Obtener promoción
- `PUT /api/promotion/{id}` - Actualizar promoción (requiere admin)
- `DELETE /api/promotion/{id}` - Eliminar promoción (requiere admin)

### Órdenes de Compra
- `GET /api/purchase_order/` - Listar órdenes
- `POST /api/purchase_order/` - Crear orden
- `GET /api/purchase_order/{id}` - Obtener orden
- `PUT /api/purchase_order/{id}` - Actualizar orden (requiere admin)
- `DELETE /api/purchase_order/{id}` - Eliminar orden (requiere admin)

### Status
- `GET /api/helloworld` - Endpoint de prueba

## Autenticación API

Para usar los endpoints protegidos, incluir el token en el header:
```
Authorization: Bearer {token}
```

El token se obtiene mediante el endpoint `/api/auth/login`.

## Notas para Estudiantes

Este proyecto está diseñado como ejemplo educativo. Algunos aspectos a considerar:

1. **Seguridad**: El sistema de tokens es simplificado. En producción, usar JWT con librerías aprobadas.
2. **Validación**: La validación de datos es básica. Implementar validación más robusta según necesidades.
3. **Email**: La funcionalidad de envío de emails está preparada pero no implementada completamente.
4. **Error Handling**: Mejorar el manejo de errores según sea necesario.
5. **Testing**: Agregar tests unitarios e integración.

## Mejoras Sugeridas

- Implementar sistema de logs más completo
- Agregar paginación en listados
- Implementar búsqueda y filtros avanzados
- Agregar tests automatizados
- Mejorar la UI/UX del panel de administración
- Implementar sistema de notificaciones
- Agregar exportación de datos (CSV, PDF)

## Licencia

Este proyecto es para fines educativos.

## Autor

Proyecto educativo para demostración de arquitectura MVC y API REST en PHP.


