# Sistema de Gestión ATECOP

Sistema web de gestión interna para la Asociación Técnica de Constructores y Profesionales (ATECOP).

## Características

- **Gestión de Socios**: Registro, modificación, consulta y baja de socios con validación de DNI/RUC mediante API externa
- **Gestión de Membresías**: Administración de planes de membresía y asignación a socios
- **Gestión de Pagos**: Registro de pagos con comprobantes, cálculo automático de vencimientos
- **Gestión de Ponentes**: Administración de ponentes/tutores para cursos
- **Gestión de Cursos**: Creación de cursos e inscripción de socios
- **Reportes**: Generación de reportes en PDF y Excel (socios morosos, vencimientos, etc.)
- **Seguridad**: Sistema de autenticación con sesiones PHP

## Tecnologías

- **Backend**: PHP 8+ (sin frameworks)
- **Base de Datos**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript Vanilla
- **Arquitectura**: MVC + DAO modular
- **Librerías**: FPDF, PhpSpreadsheet (vía Composer)

## Requisitos

- PHP 8.0 o superior
- MySQL 5.7 o superior
- Apache con mod_rewrite habilitado
- Composer (para instalar dependencias)
- XAMPP (recomendado para desarrollo local)

## Instalación

### 1. Clonar o descargar el proyecto

Coloca los archivos en la carpeta `htdocs` de XAMPP (o el directorio web de tu servidor).

### 2. Configurar la base de datos

1. Crea una base de datos MySQL llamada `atecop_db`
2. Importa el script SQL:
   \`\`\`bash
   mysql -u root -p atecop_db < sql/create_database.sql
   \`\`\`

### 3. Configurar la conexión

1. Copia el archivo de configuración de ejemplo:
   \`\`\`bash
   cp config/config.php.example config/config.php
   \`\`\`

2. Edita `config/config.php` con tus credenciales de base de datos:
   \`\`\`php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'atecop_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   \`\`\`

3. Configura la API Key de Perú Dev (opcional, para validación de DNI/RUC):
   \`\`\`php
   define('API_PERUDEV_KEY', 'tu_api_key_aqui');
   \`\`\`

### 4. Instalar dependencias

\`\`\`bash
composer install
\`\`\`

### 5. Configurar permisos

Asegúrate de que el directorio `public/uploads/` tenga permisos de escritura:

\`\`\`bash
chmod -R 755 public/uploads/
\`\`\`

### 6. Acceder al sistema

1. Inicia Apache y MySQL en XAMPP
2. Abre tu navegador y ve a: `http://localhost/sistema_atecop/`
3. Credenciales por defecto:
   - Usuario: `admin`
   - Contraseña: `admin123`

**IMPORTANTE**: Cambia la contraseña por defecto después del primer inicio de sesión.

## Estructura del Proyecto

\`\`\`
sistema_atecop/
├── config/              # Configuración global
├── public/              # Archivos públicos (CSS, JS, imágenes, uploads)
├── modulos/             # Módulos funcionales (MVC+DAO)
│   ├── socios/
│   ├── membresias/
│   ├── pagos/
│   ├── ponentes/
│   ├── cursos/
│   ├── reportes/
│   ├── seguridad/
│   └── layouts/         # Header y footer compartidos
├── util_global/         # Utilidades globales (Database, API)
├── vendor/              # Dependencias de Composer
├── sql/                 # Scripts SQL
├── index.php            # Front Controller
├── autoload.php         # Autoloader PSR-4
└── composer.json        # Configuración de Composer
\`\`\`

## Módulos del Sistema

### 1. Seguridad
- Login/Logout
- Gestión de sesiones
- Dashboard principal

### 2. Socios
- Registro con validación de DNI/RUC (API Perú Dev)
- Modificación de datos
- Consulta de perfil detallado
- Dar de baja

### 3. Membresías
- CRUD de planes de membresía
- Asignación de planes a socios

### 4. Pagos
- Registro de pagos con comprobantes
- Cálculo automático de vencimientos
- Historial de pagos
- Anulación de pagos

### 5. Ponentes
- CRUD de ponentes
- Validación de DNI

### 6. Cursos
- CRUD de cursos
- Inscripción de socios
- Gestión de cupos
- Lista de inscritos

### 7. Reportes
- Socios morosos (PDF/Excel)
- Próximos vencimientos (PDF/Excel)
- Detalle de socio (PDF)
- Socios para inhabilitar (PDF/Excel)

## Seguridad

- Contraseñas hasheadas con `password_hash()`
- Sentencias preparadas PDO (prevención de SQL Injection)
- Protección XSS con `htmlspecialchars()`
- Validación de sesiones en todas las páginas protegidas
- Protección de archivos sensibles vía `.htaccess`

## API Externa

El sistema utiliza la API de Perú Dev para validar DNI y RUC:
- Endpoint DNI: `https://api.perudevs.com/api/v1/dni/`
- Endpoint RUC: `https://api.perudevs.com/api/v1/ruc/`

Para obtener una API Key gratuita, visita: https://api.perudevs.com/

## Soporte

Para reportar problemas o solicitar nuevas funcionalidades, contacta al equipo de desarrollo de ATECOP.

## Licencia

Sistema propietario de ATECOP. Todos los derechos reservados.

## Versión

1.0.0 - Sistema inicial completo
