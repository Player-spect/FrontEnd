
<?php require_once '../BackEnd/includes/navegacion.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNK Inmobiliaria - Recuperar Contraseña | Restablecer Acceso</title>
    <meta name="description" content="Recupera tu contraseña en PNK Inmobiliaria para volver a acceder a tu cuenta y gestionar propiedades en Coquimbo.">
    <meta name="keywords" content="recuperar contraseña inmobiliaria, reset password PNK, restablecer acceso Coquimbo">
    <meta name="author" content="PNK Inmobiliaria">
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="https://www.pnk-inmobiliaria.cl/password-recovery.html">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<a href="#main-content" class="skip-link">Saltar al contenido principal</a>

<header role="banner">
    <h1>PNK Inmobiliaria</h1>
    <nav aria-label="Navegación principal">
        <ul>
            <li><a href="../index.html">Inicio</a></li>
            <li><a href="login.php">Iniciar Sesión</a></li>
            <li><a href="register/register-gestor.html">Registrar Gestor</a></li>
            <li><a href="register/register-propietario.html">Registrar Propietario</a></li>
        </ul>
    </nav>
</header>

<main id="main-content" role="main">
    <section aria-labelledby="recovery-heading">
        <h2 id="recovery-heading">Recuperar Contraseña</h2>
        <p>Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña.</p>

        <div id="form-messages" role="status" aria-live="polite" aria-atomic="true" class="sr-only"></div>

        <form id="recovery-form" action="#" method="post" novalidate
              aria-labelledby="recovery-heading">
            <p class="sr-only">Los campos marcados con * son obligatorios.</p>

            <label for="recovery-email">
                Correo Electrónico: <span aria-hidden="true">*</span>
            </label>
            <input type="email" id="recovery-email" name="email" required
                   autocomplete="email"
                   aria-required="true"
                   aria-describedby="recovery-help"
                   spellcheck="false">
            <span id="recovery-help" class="sr-only">Ingresa el correo electrónico asociado a tu cuenta para recibir instrucciones de recuperación</span>

            <button type="submit">Enviar Instrucciones</button>
        </form>

        <p><a href="login.php">← Volver al Inicio de Sesión</a></p>
    </section>
</main>

<footer role="contentinfo">
    <p>&copy; 2026 PNK Inmobiliaria. Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('recovery-form');
        const emailField = document.getElementById('recovery-email');

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Validación inválida',
                text: message,
                confirmButtonText: 'Aceptar'
            });
        }

        function validarEmail(value) {
            if (!value) return false;
            const email = value.trim();
            if ((email.match(/@/g) || []).length !== 1) return false;
            return /^[A-Za-z0-9]{3,}@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(email);
        }

        function handleSubmit(event) {
            event.preventDefault();
            if (!emailField || !validarEmail(emailField.value)) {
                showError('El correo electrónico debe ser válido, incluir solo un @ y tener al menos un punto después del dominio.');
                return;
            }
            form.submit();
        }

        if (form) {
            form.addEventListener('submit', handleSubmit);
        }
    });
</script>
</body>
</html>
