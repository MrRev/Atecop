# Sistema de Gestión ATECOP v2.0

Sistema web de gestión interna para la Asociación Técnica de Constructores y Profesionales (ATECOP). Esta versión incluye mejoras significativas en la interfaz de usuario, validación de datos y seguridad.

## Características Principales

### Gestión de Usuarios y Accesos
- **Sistema de Roles**: Administración granular de permisos
- **Validación Avanzada**: Verificación en tiempo real de DNI mediante API
- **Perfiles Completos**: Vista detallada de información de usuarios
- **Estados Dinámicos**: Control de usuarios activos/inactivos

### Gestión de Socios
- **Validación DNI/RUC**: Integración con API Perú Dev
- **Perfiles Detallados**: Información completa de cada socio
- **Gestión de Estados**: Control de membresías activas/inactivas
- **Historial**: Seguimiento de pagos y actividades

### Módulos Operativos
- **Gestión de Membresías**: Planes y asignaciones
- **Control de Pagos**: Comprobantes y vencimientos
- **Administración de Cursos**: Inscripciones y seguimiento
- **Gestión de Ponentes**: Perfiles y asignaciones

### Reportes y Análisis
- **Exportación Múltiple**: Formatos PDF y Excel
- **Reportes Dinámicos**: Filtros personalizables
- **Estadísticas**: Dashboard con KPIs principales

## Tecnologías y Stack

### Backend
- **PHP**: Versión 8.1 o superior
- **MySQL**: Versión 8.0 o superior
- **Apache**: 2.4 con mod_rewrite

### Frontend
- **HTML5 & CSS3**: Diseño responsive
- **JavaScript**: ES6+, Fetch API
- **Bootstrap**: v5.2 para componentes UI
- **Font Awesome**: Para iconografía

### Arquitectura y Patrones
- **MVC**: Arquitectura Modelo-Vista-Controlador
- **DAO**: Data Access Objects para abstracción de datos
- **Singleton**: Para conexiones de base de datos
- **Factory**: Para creación de objetos complejos

### Librerías y Dependencias
- **FPDF**: Generación de PDFs
- **PhpSpreadsheet**: Manejo de Excel
- **HTMLPurifier**: Seguridad XSS
- **Composer**: Gestión de dependencias

## Requisitos del Sistema

### Servidor
- PHP 8.1+
- MySQL 8.0+
- Apache 2.4+
- mod_rewrite habilitado
- Extensiones PHP:
  - PDO_MySQL
  - GD
  - mbstring
  - zip
  - xml

### Desarrollo Local
- XAMPP (última versión)
- Composer 2.0+
- Git

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

### 3.b Archivo de configuración de ejemplo

Se incluye un archivo de ejemplo `config/config.php.example`. No subas credenciales reales al repositorio.

Para crear tu archivo de configuración local copia el ejemplo y edita los valores:

```bash
cp config/config.php.example config/config.php
# en Windows PowerShell
Copy-Item config\config.php.example config\config.php
```

Rellena `config/config.php` con tus credenciales (DB, API keys, BASE_URL, etc.).

Recuerda que `config/config.php` está en `.gitignore` por motivos de seguridad.
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

1. Inicia los servicios en XAMPP:
   ```bash
   # Windows - Usar XAMPP Control Panel
   # Linux/Mac
   sudo /opt/lampp/lampp start
   ```

2. Accede a la aplicación:
   - URL: `http://localhost/Atecop/`
   - Credenciales por defecto:
     ```
     Usuario: admin
     Contraseña: admin123
     ```

⚠️ **IMPORTANTE**: 
- Cambiar la contraseña predeterminada inmediatamente
- Configurar un certificado SSL para producción
- Revisar los permisos de archivos en producción

## Estructura del Proyecto v2.0

```
Atecop/
├── config/                 # Configuración global
│   └── config.php         # Variables de entorno
├── public/                # Archivos públicos
│   ├── css/              # Estilos
│   ├── js/               # JavaScript
│   ├── img/              # Imágenes
│   └── uploads/          # Archivos subidos
├── modulos/              # Módulos del sistema
│   ├── cursos/          # Gestión de cursos
│   ├── layouts/         # Plantillas comunes
│   ├── membresias/      # Planes y membresías
│   ├── pagos/           # Control de pagos
│   ├── ponentes/        # Gestión de ponentes
│   ├── reportes/        # Generación de informes
│   ├── seguridad/       # Autenticación y permisos
│   ├── socios/          # Gestión de socios
│   └── usuarios/        # Admin. usuarios
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
