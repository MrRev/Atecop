# Changelog

## [1.1.0] - 2025-11-01

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

#### Módulo de Reportes
- Nuevas vistas HTML para reportes
  - VistaReporteSociosMorosos.php
  - VistaReporteProximosVencimientos.php

### 🐞 Correcciones de Errores

#### Sesión y AJAX
- Corrección de redireccionamiento al Dashboard
- Actualización de configuración SameSite a 'Lax'
- Implementación de cabeceras X-Requested-With

#### JSON/HTML
- Corrección de mezcla de respuestas
- Implementación de exit después de json_encode()
- Separación clara de respuestas AJAX y HTML

#### Enrutamiento
- Corrección de rutas en index.php
- Alineación de nombres de acciones
- Corrección de case breaks faltantes

#### Carga de Assets
- Corrección de RewriteBase en .htaccess
- Unificación de rutas de assets usando BASE_URL
- Implementación consistente en header.php y footer.php

#### Errores de Tipado y Métodos
- Corrección de uso de objetos vs arrays
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