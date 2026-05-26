<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../FrontEnd/dashboard/dashboard-" . $_SESSION['user_rol'] . ".php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNK Inmobiliaria - Iniciar Sesión | Acceso a tu Cuenta</title>
    <meta name="description" content="Inicia sesión en PNK Inmobiliaria para acceder a tu dashboard y gestionar propiedades en la Región de Coquimbo.">
    <meta name="keywords" content="iniciar sesión inmobiliaria, login PNK, acceso cuenta propiedades Coquimbo">
    <meta name="author" content="PNK Inmobiliaria">
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="https://www.pnk-inmobiliaria.cl/login.html">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<a href="#main-content" class="skip-link">Saltar al contenido principal</a>

<header role="banner">
    <h1>PNK Inmobiliaria</h1>
    <nav aria-label="Navegación principal">
        <ul>
            <li><a href="../index.html">Inicio</a></li>
            <li><a href="login.html" aria-current="page">Iniciar Sesión</a></li>
            <li><a href="register/register-gestor.html">Registrar Gestor</a></li>
            <li><a href="register/register-propietario.html">Registrar Propietario</a></li>
        </ul>
    </nav>
</header>

<main id="main-content" role="main">
    <section aria-labelledby="login-heading">
        <h2 id="login-heading">Iniciar Sesión</h2>

        <!-- Región de mensajes de error/éxito -->
        <div id="form-messages" role="alert" aria-live="assertive" aria-atomic="true" class="sr-only"></div>

        <form id="login-form" action="#" method="post"
              aria-labelledby="login-heading">
            <p class="sr-only">Los campos marcados con * son obligatorios.</p>

            <label for="email">
                Correo Electrónico: <span aria-hidden="true">*</span>
            </label>
            <input type="email" id="email" name="email" required
                   autocomplete="email"
                   aria-required="true"
                   aria-describedby="email-help"
                   spellcheck="false">
            <span id="email-help" class="sr-only">Ingresa tu correo electrónico registrado</span>

            <label for="password">
                Contraseña: <span aria-hidden="true">*</span>
            </label>
            <input type="password" id="password" name="password" required
                   autocomplete="current-password"
                   aria-required="true"
                   aria-describedby="password-help">
            <span id="password-help" class="sr-only">Ingresa tu contraseña</span>

            <button type="submit">Iniciar Sesión</button>
        </form>

        <p><a href="password-recovery.php">¿Olvidaste tu contraseña?</a></p>
        <p>¿No tienes cuenta?
            <a href="register/register-gestor.html">Regístrate como Gestor</a>
            o
            <a href="register/register-propietario.html">como Propietario</a>
        </p>
    </section>
</main>

<footer role="contentinfo">
    <p>&copy; 2026 PNK Inmobiliaria. Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('login-form');
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Acceso Denegado',
                text: message,
                confirmButtonText: 'Aceptar'
            });
        }

        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Acceso Permitido',
                text: message,
                confirmButtonText: 'Continuar'
            });
        }

        async function handleSubmit(event) {
            event.preventDefault();

            const email = emailField.value;
            const password = passwordField.value;

            if (!email || !password) {
                showError('Usuario y contraseña no pueden quedar en blanco.');
                return;
            }

            try {
                const response = await fetch('../BackEnd/auth/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        email: email,
                        password: password
                    })
                });

                const result = await response.json();

                if (!result.success) {
                    showError(result.message || 'Credenciales incorrectas.');
                    return;
                }

                await showSuccess(result.message || 'Inicio de sesión exitoso.');

                if (result.redirect) {
                    window.location.href = result.redirect;
                }
            } catch (error) {
                showError('Error de conexión. Intenta nuevamente más tarde.');
            }
        }

        if (form) {
            form.addEventListener('submit', handleSubmit);
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const response = await fetch('../../BackEnd/api/check_session.php');
        const data = await response.json();

        if (data.logged_in) {
            window.location.href = `../dashboard/dashboard-${data.rol}.php`;
        }
    });
</script>
</body>
</html>
