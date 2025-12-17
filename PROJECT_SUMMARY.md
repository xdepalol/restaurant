# Restaurant Management System - Project Summary

## Overview

Este proyecto es un sistema completo de gestión de restaurante desarrollado en PHP puro, diseñado como ejemplo educativo para estudiantes. Demuestra las mejores prácticas en desarrollo web con arquitectura MVC y API REST.

## Características Implementadas

### ✅ API REST Completa

- **Endpoints CRUD** para todas las entidades:
  - Usuarios (`/api/user/`)
  - Categorías (`/api/category/`)
  - Productos (`/api/product/`)
  - Promociones (`/api/promotion/`)
  - Órdenes de Compra (`/api/purchase_order/`)

- **Autenticación**:
  - Sistema de tokens simétricos
  - Endpoint de login (`/api/auth/login`)
  - Endpoint para obtener token desde sesión (`/api/auth/token`)
  - Middleware de autenticación con validación de roles

- **Validación de Datos**:
  - Clase Validator con reglas comunes
  - Validación en todos los endpoints POST/PUT
  - Mensajes de error descriptivos

- **Manejo de Transacciones**:
  - Órdenes de compra usan transacciones de base de datos
  - Rollback automático en caso de error

- **Documentación API**:
  - Comentarios apidoc en todos los endpoints
  - Generación de documentación con `apidoc -i public/api -o docs`

### ✅ Sitio Web MVC

**Arquitectura**:
- Router simple en `public/index.php`
- Controladores en `src/controllers/`
- Modelos en `src/models/`
- Vistas en `src/views/`
- Sin autoloader (usa `require_once`)

**Área Pública**:
- HomeController: Página principal, sobre nosotros, legal
- AuthController: Login y registro
- ShoppingController: Listado de productos, carrito, checkout
- AccountController: Perfil de usuario, historial de pedidos

**Carrito de Compras**:
- Almacenamiento en localStorage
- Agregar/eliminar productos
- Actualizar cantidades
- Cálculo automático de totales

**Panel de Administración**:
- Acceso restringido a usuarios con rol 'admin'
- Interfaz con pestañas (tabs) de Bootstrap
- Secciones:
  - Usuarios: Listar, editar, eliminar
  - Pedidos: Listar con filtros (cliente, fecha, promoción)
  - Promociones: CRUD completo
  - Categorías: CRUD completo
  - Productos: CRUD completo
- Datos cargados vía API REST

### ✅ Base de Datos

- Esquema completo en `sql/schema.sql`
- Relaciones entre tablas correctamente definidas
- Datos de ejemplo incluidos
- Usuario administrador por defecto

### ✅ Seguridad

- Contraseñas encriptadas con `password_hash()`
- Tokens con expiración
- Validación de permisos por rol
- Códigos HTTP apropiados (401, 403, 404, etc.)
- Protección contra inyección SQL (PDO prepared statements)

## Estructura de Archivos

```
restaurant/
├── config/                 # Configuración
│   ├── config.php         # Configuración general
│   └── database.php       # Conexión a base de datos
├── public/                 # Punto de entrada público
│   ├── index.php          # Router principal
│   ├── api/               # Endpoints API
│   │   ├── router.php     # Router de API
│   │   ├── helloworld.php
│   │   ├── auth/
│   │   ├── user/
│   │   ├── category/
│   │   ├── product/
│   │   ├── promotion/
│   │   └── purchase_order/
│   └── assets/            # CSS, JS, imágenes
├── src/
│   ├── controllers/       # Controladores MVC
│   ├── models/            # Modelos de datos
│   ├── views/             # Vistas/templates
│   ├── middleware/        # Middleware (auth)
│   └── utils/             # Utilidades
├── sql/
│   └── schema.sql         # Esquema de base de datos
└── docs/                  # Documentación API (generada)
```

## Puntos Educativos Destacados

1. **Arquitectura MVC**: Separación clara de responsabilidades
2. **API REST**: Diseño RESTful con métodos HTTP apropiados
3. **Autenticación**: Sistema de tokens simétricos
4. **Validación**: Validación de datos en múltiples capas
5. **Transacciones**: Uso de transacciones de base de datos
6. **Seguridad**: Buenas prácticas de seguridad web
7. **Documentación**: Documentación API con apidoc
8. **Frontend**: Integración de Bootstrap y JavaScript moderno

## Mejoras Futuras Sugeridas

1. **Testing**: Agregar tests unitarios e integración
2. **Email**: Implementar envío de emails de confirmación
3. **Logs**: Sistema de logs más completo
4. **Paginación**: Agregar paginación en listados
5. **Búsqueda**: Implementar búsqueda avanzada
6. **Exportación**: Exportar datos a CSV/PDF
7. **Imágenes**: Sistema de subida de imágenes
8. **Cache**: Implementar cache para mejorar rendimiento
9. **JWT**: Migrar a JWT library en lugar de tokens simples
10. **Composer**: Agregar gestión de dependencias

## Notas para Instructores

- El proyecto está diseñado para ser educativo pero funcional
- Los estudiantes pueden usar esto como referencia
- Se pueden asignar tareas de mejora o extensión
- El código está comentado para facilitar el aprendizaje
- La estructura permite fácil extensión

## Notas para Estudiantes

- Estudiar la estructura MVC
- Entender cómo funciona el router
- Analizar el sistema de autenticación
- Revisar la validación de datos
- Practicar agregando nuevas funcionalidades
- Experimentar con la API usando herramientas como Postman

## Tecnologías Utilizadas

- PHP 7.4+
- MariaDB/MySQL
- Bootstrap 5
- JavaScript (ES6+)
- apidoc (para documentación)

## Licencia

Proyecto educativo - Uso libre para fines educativos.



