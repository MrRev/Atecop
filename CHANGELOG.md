# Changelog

## [2.0.0] - 2025-11-01

### 🎯 Características Principales

#### Sistema de Usuarios
- Nueva interfaz de gestión de usuarios
- Validación en tiempo real de DNI con API
- Sistema mejorado de roles y permisos
- Perfiles de usuario detallados
- Control dinámico de estados de usuario

#### Mejoras de UX/UI
- Implementación de Bootstrap 5
- Feedback visual en tiempo real
- Nuevos componentes interactivos
- Validación de formularios mejorada
- Sistema de notificaciones

#### Seguridad
- Implementación de HTMLPurifier
- Mejora en manejo de sesiones
- Protección contra XSS
- Sanitización de datos mejorada
- Validación robusta de entradas

#### Optimizaciones
- Mejora en consultas SQL
- Caché de consultas frecuentes
- Optimización de carga de assets
- Reducción de llamadas AJAX
- Mejor manejo de errores

### 🔧 Correcciones

#### Accesibilidad
- Labels en formularios
- Atributos ARIA
- Contraste de colores
- Navegación por teclado
- Mensajes de error claros

#### Recursos
- Rutas de CSS/JS corregidas
- Sistema de imágenes mejorado
- Gestión de uploads optimizada
- Assets comprimidos
- Carga condicional de recursos

## [1.1.0] - 2025-10-15

### 🚀 Nuevas Características y Mejoras

#### Reactivar Socios
- Nueva funcionalidad para reactivar socios inactivos
- Implementación en DAO, Controlador y Vista
- Botón dinámico "Activar/Baja" según estado

#### Mejoras de UX con AJAX
- Conversión de acciones de recarga a peticiones AJAX
- Implementación de feedback visual con alerts
- Recarga automática después de acciones exitosas

#### Lógica de Negocio Mejorada
- Filtrado de planes activos en formulario de nuevo socio
- Protección de campos clave en formularios de edición
- Implementación de readonly en DNI y nombre completo

### 🐞 Correcciones de Errores

#### Sesión y AJAX
- Corrección de redireccionamiento al Dashboard
- Actualización de configuración SameSite a 'Lax'
- Implementación de cabeceras X-Requested-With

#### JSON/HTML
- Corrección de mezcla de respuestas
- Implementación de exit después de json_encode()
- Separación clara de respuestas AJAX y HTML

## [1.0.0] - 2024-01-01

### 🎉 Versión Inicial
- Implementación base del sistema
- Módulos principales
- Estructura MVC básica
- Actualización de nombres de métodos para consistencia
- Corrección de referencias a métodos inexistentes

#### Lógica de Formularios
- Corrección de actions en formularios
- Implementación de redirecciones apropiadas
- Corrección de pantallazos blancos

#### Filtros de Socios
- Corrección de JOIN en consultas SQL
- Actualización de métodos de listado
- Implementación correcta de filtros

### 📝 Notas Técnicas
- Revisión completa de manejo de sesiones
- Mejora en la consistencia de nombres de métodos
- Optimización de consultas SQL
- Implementación de mejores prácticas AJAX