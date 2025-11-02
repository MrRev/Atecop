<?php
require_once dirname(__DIR__, 3) . '/config/config.php';

// No iniciamos sesión aquí porque ya se inicia en index.php
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
    <link rel="stylesheet" href="/css/Login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
    color: 
    #fff;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: #fff;
}

.container {
    position: relative;
    width: 750px;
    height: 450px;
    border: 2px solid #002E5D;
    box-shadow: 0 0 25px #002E5D;
    overflow: hidden;
}

.container .Form-box {
    position: absolute;
    top: 0;
    width: 50%;
    height: 100%;
    display: flex;
    justify-content: center;
    flex-direction: column;
}

.Form-box.Login {
    left: 0;
    padding: 0 40px;
}

.Form-box h2 {
    font-size: 32px;
    text-align: center;
    color: #002E5D;
}

.Form-box .input-box {
    position: relative;
    width: 100%;
    height: 50px;
    margin-top: 25px;
}

.input-box input {
    width: 100%;
    height: 100%;
    background: transparent;
    border: none;
    outline: none;
    font-size: 16px;
    color: #002E5D;
    font-weight: 600;
    border-bottom: 2px solid #002E5D;
    padding: 23px;
    transition: .5s;
}

.input-box input:focus,
.input-box input:valid {
    border-bottom: 2px solid #002E5D;
}

.input-box label {
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    font-size: 16px;
    color: #002E5D;
    transition: .5s;
}

.input-box input:focus ~ label,
.input-box input:valid ~ label {
    top: -5px;
    color: #002E5D;
}

.input-box i {
    position: absolute;
    top: 50%;
    right: 0;
    font-size: 18px;
    transform: translateY(-50%);
    transition: .5s;
    color: #000;
}

.input-box input:focus ~ i,
.input-box input:valid ~ i {
    color: #002E5D;
}

.btn {
    position: relative;
    width: 100%;
    height: 45px;
    background: transparent;
    border-radius: 40px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    border: 2px solid #002E5D;
    overflow: hidden;
    z-index: 1;
    color: #002E5D;
}

.btn::before {
    content: "";
    position: absolute;
    height: 300%;
    width: 100%;
    background: linear-gradient(#25252b, #002E5D, #25252b, #002E5D);
    top: -100%;
    left: 0;
    z-index: 0;
    transition: .5s;
}

.btn:hover::before {
    top: 0;
}

.btn span {
    position: relative;
    z-index: 2; /* Texto visible por encima del fondo animado */
}

.regi-link {
    margin-top: 14px;
    text-align: center;
    margin: 20px 0 10px;
}

.regi-link a{
    text-decoration: none;
    color: #002E5D;
    font-weight: 600;
}

.regi-link a:hover{
    text-decoration: underline;
}

.info-content {
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
    width: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;  
    align-items: flex-end;     
    text-align: right;     
    padding: 0 60px;          
    z-index: 2;               
    background: transparent;  
}

.info-content {
    right: 0;
    text-align: right;
    padding: 0 40px 60px 150px; 
}

.info-content h2 {
    text-transform: uppercase;
    font-size: 36px;
    line-height: 1.3;
    margin-bottom: 10px;
}

.info-content p {
    font-size: 16px;
    max-width: 320px;
}


.container .curved-shape{
    position: absolute;
    right: 0;
    top: -5px;
    height: 600px;
    width: 850px;
    background: linear-gradient(45deg,#25252b,#002E5D);
    transform: rotate(10deg) skewY(40deg);
    transform-origin: bottom right;
}
</style>

<body>
    <div class="container">
        <div class="curved-shape"></div>

        <div class="Form-box Login">
            <h2>Login</h2>

            <?php if (isset($_SESSION['error_login'])): ?>
                <div class="regi-link" style="color: red; text-align: center; margin-bottom: 15px;">
                    <?php 
                        echo htmlspecialchars($_SESSION['error_login']); 
                        unset($_SESSION['error_login']);
                    ?>
                </div>
            <?php endif; ?>
<<<<<<< HEAD
            
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
=======

            <form action="<?php echo BASE_URL; ?>/index.php?modulo=seguridad&accion=procesarLogin" method="POST">
                <div class="input-box">
                    <input type="text" name="usuario" required>
                    <label>Usuario</label>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box">
                    <input type="password" name="password" required>
                    <label>Contraseña</label>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <div class="input-box">
                    <button class="btn" type="submit"><span>Login</span></button>
                </div>

                <div class="regi-link">
                    <p><a href="#">¿Olvidaste tu contraseña?</a></p>
                </div>
>>>>>>> 6af4e7485339a38b226a0b19b8efcb562bdb5489
            </form>
        </div>

        <div class="info-content">
            <h2>¡Bienvenido!</h2>
            <p>Accede al panel de gestión para crear, editar y supervisar usuarios fácilmente.</p>
        </div>
    </div>
</body>
</html>
