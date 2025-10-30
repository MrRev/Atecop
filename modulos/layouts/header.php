<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['idadmin'])) {
    header('Location: ' . BASE_URL . '/index.php?modulo=seguridad&accion=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/bootstrap.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color: #002e5d;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }
        .navbar-brand img {
            height: 40px;
        }
        .navbar-nav {
            display: flex;
            gap: 0;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        .navbar-nav .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }
        .navbar-user .user-name {
            font-size: 14px;
        }
        .btn-logout {
            background-color: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .btn-logout:hover {
            background-color: #bb2d3b;
            color: white;
        }
        main {
            flex: 1;
            padding: 2rem 0;
        }
        footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 2rem 0;
            text-align: center;
            color: #707070;
            font-size: 14px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="<?php echo BASE_URL; ?>/index.php?modulo=dashboard" class="navbar-brand">
                <img src="<?php echo IMG_URL; ?>/logo-atecop.png" alt="ATECOP">
                <span>ATECOP</span>
            </a>
            
            <ul class="navbar-nav">
                <li><a href="<?php echo BASE_URL; ?>/index.php?modulo=dashboard" class="nav-link">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL; ?>/index.php?modulo=socios&accion=listar" class="nav-link">Socios</a></li>
                <li><a href="<?php echo BASE_URL; ?>/index.php?modulo=membresias&accion=listar" class="nav-link">Membresías</a></li>
                <li><a href="<?php echo BASE_URL; ?>/index.php?modulo=pagos&accion=listar" class="nav-link">Pagos</a></li>
                <li><a href="<?php echo BASE_URL; ?>/index.php?modulo=ponentes&accion=listar" class="nav-link">Ponentes</a></li>
                <li><a href="<?php echo BASE_URL; ?>/index.php?modulo=cursos&accion=listar" class="nav-link">Cursos</a></li>
                <li><a href="<?php echo BASE_URL; ?>/index.php?modulo=reportes&accion=menu" class="nav-link">Reportes</a></li>
            </ul>
            
            <div class="navbar-user">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombrecompleto'] ?? $_SESSION['usuario']); ?></span>
                <a href="<?php echo BASE_URL; ?>/index.php?modulo=seguridad&accion=logout" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    
    <main>
        <div class="container">
