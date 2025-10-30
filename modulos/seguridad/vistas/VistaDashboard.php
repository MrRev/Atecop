<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<style>
/* Contenedor principal del dashboard */
.dashboard-container {
    padding: 2rem;
    font-family: Arial, sans-serif;
    background-color: #f5f6fa;
    min-height: 100vh;
}

/* Cabecera */
.dashboard-header h1 {
    margin: 0;
    font-size: 2rem;
    color: #002E5D;
}

.dashboard-header p {
    margin-top: 0.5rem;
    font-size: 1rem;
    color: #555;
}

/* Grid de tarjetas */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

/* Tarjetas */
.dashboard-card {
    display: flex;
    align-items: flex-start;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    padding: 1rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

/* Icono de la tarjeta */
.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

/* Contenido de la tarjeta */
.card-content h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.2rem;
    color: #002E5D;
}

.card-content p {
    margin: 0 0 1rem 0;
    font-size: 0.9rem;
    color: #666;
}

/* Botón */
.btn.btn-secondary {
    display: inline-block;
    text-decoration: none;
    padding: 0.5rem 1rem;
    background-color: #3BAFDA;
    color: #fff;
    border-radius: 8px;
    transition: background-color 0.2s;
}

.btn.btn-secondary:hover {
    background-color: #2a8acb;
}
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Panel de Control</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombrecompleto'] ?? $_SESSION['usuario']); ?></p>
    </div>
    
    <div class="dashboard-grid">
        <!-- Tarjeta: Socios -->
        <div class="dashboard-card">
            <div class="card-icon" style="background-color: #002E5D;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="card-content">
                <h3>Gestión de Socios</h3>
                <p>Administrar socios, registros y perfiles</p>
                <a href="<?php echo BASE_URL; ?>/index.php?modulo=socios&accion=listar" class="btn btn-secondary">
                    Ir al módulo
                </a>
            </div>
        </div>
        
        <!-- Tarjeta: Membresías -->
        <div class="dashboard-card">
            <div class="card-icon" style="background-color: #3BAFDA;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
            </div>
            <div class="card-content">
                <h3>Planes de Membresía</h3>
                <p>Gestionar planes y asignaciones</p>
                <a href="<?php echo BASE_URL; ?>/index.php?modulo=membresias&accion=listar" class="btn btn-secondary">
                    Ir al módulo
                </a>
            </div>
        </div>
        
        <!-- Tarjeta: Pagos -->
        <div class="dashboard-card">
            <div class="card-icon" style="background-color: #002E5D;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
            </div>
            <div class="card-content">
                <h3>Registro de Pagos</h3>
                <p>Registrar y gestionar pagos</p>
                <a href="<?php echo BASE_URL; ?>/index.php?modulo=pagos&accion=registrar" class="btn btn-secondary">
                    Ir al módulo
                </a>
            </div>
        </div>
        
        <!-- Tarjeta: Ponentes -->
        <div class="dashboard-card">
            <div class="card-icon" style="background-color: #3BAFDA;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <div class="card-content">
                <h3>Gestión de Ponentes</h3>
                <p>Administrar ponentes y tutores</p>
                <a href="<?php echo BASE_URL; ?>/index.php?modulo=ponentes&accion=listar" class="btn btn-secondary">
                    Ir al módulo
                </a>
            </div>
        </div>
        
        <!-- Tarjeta: Cursos -->
        <div class="dashboard-card">
            <div class="card-icon" style="background-color: #002E5D;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </div>
            <div class="card-content">
                <h3>Gestión de Cursos</h3>
                <p>Administrar cursos e inscripciones</p>
                <a href="<?php echo BASE_URL; ?>/index.php?modulo=cursos&accion=listar" class="btn btn-secondary">
                    Ir al módulo
                </a>
            </div>
        </div>
        
        <!-- Tarjeta: Reportes -->
        <div class="dashboard-card">
            <div class="card-icon" style="background-color: #3BAFDA;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <div class="card-content">
                <h3>Reportes</h3>
                <p>Generar reportes y estadísticas</p>
                <a href="<?php echo BASE_URL; ?>/index.php?modulo=reportes&accion=menu" class="btn btn-secondary">
                    Ir al módulo
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
