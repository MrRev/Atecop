<?php
require_once dirname(__DIR__, 3) . '/config/config.php';

// Comprobar si la sesión NO está iniciada antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['idadmin'])) {
    header('Location: ' . BASE_URL . '/index.php?modulo=dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/bootstrap.css">
    <style>
        body {
            background: linear-gradient(135deg, #002e5d 0%, #3bafda 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo img {
            max-width: 80px;
            height: auto;
        }
        .login-title {
            text-align: center;
            color: #002e5d;
            margin-bottom: 10px;
            font-size: 24px;
            font-weight: bold;
        }
        .login-subtitle {
            text-align: center;
            color: #707070;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group label {
            color: #002e5d;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-control {
            border: 1px solid #ddd;
            padding: 10px 15px;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #3bafda;
            box-shadow: 0 0 0 0.2rem rgba(59, 175, 218, 0.25);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            margin-top: 20px;
        }
        .alert {
            margin-bottom: 20px;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #707070;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <img src="<?php echo IMG_URL; ?>/logo-atecop.png" alt="ATECOP Logo">
            </div>
            
            <h1 class="login-title">ATECOP</h1>
            <p class="login-subtitle">Sistema de Gestión</p>
            
            <?php if (isset($_SESSION['error_login'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php 
                    echo htmlspecialchars($_SESSION['error_login']); 
                    unset($_SESSION['error_login']);
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo BASE_URL; ?>/index.php?modulo=seguridad&accion=procesarLogin" method="POST" id="formLogin">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input 
                        type="text" 
                        id="usuario" 
                        name="usuario" 
                        class="form-control" 
                        required 
                        autofocus 
                        placeholder="Ingrese su usuario"
                        autocomplete="username"
                    >
                </div>             
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        required 
                        placeholder="Ingrese su contraseña"
                        autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-primary btn-login">Iniciar Sesión</button>
            </form>
            
            <div class="login-footer">
                <p>&copy; <?php echo date('Y'); ?> ATECOP - Todos los derechos reservados</p>
                <p>Versión <?php echo APP_VERSION; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
