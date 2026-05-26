<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<header role="banner">
    <h1>PNK Inmobiliaria</h1>
    <nav aria-label="Navegación principal">
        <ul>
            <li><a href="../../index.html">Inicio</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="../../FrontEnd/dashboard/dashboard-<?php echo $_SESSION['user_rol']; ?>.php">Mi Dashboard</a></li>
                <li><a href="../../BackEnd/auth/logout.php">Cerrar Sesión</a></li>
            <?php else: ?>
                <li><a href="../../FrontEnd/login.php">Iniciar Sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
