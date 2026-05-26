<?php require_once '../../BackEnd/includes/session_checker.php'; ?>
<?php require_once '../../BackEnd/includes/navegacion.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNK Inmobiliaria - Dashboard Gestor Inmobiliario</title>
    <meta name="description" content="Dashboard para gestores inmobiliarios en PNK Inmobiliaria. Gestiona propiedades en venta y alquiler en la Región de Coquimbo.">
    <meta name="keywords" content="dashboard gestor inmobiliario, gestionar propiedades Coquimbo, panel gestor PNK">
    <meta name="author" content="PNK Inmobiliaria">
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="https://www.pnk-inmobiliaria.cl/dashboard/dashboard-gestor.html">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<a href="#main-content" class="skip-link">Saltar al contenido principal</a>

<main id="main-content" role="main">
    <section aria-labelledby="dashboard-title">
        <h2 id="dashboard-title">Gestión de Propiedades</h2>
        <div class="dashboard" role="region" aria-labelledby="dashboard-title">
            <article class="card">
                <h3>Registrar Propiedad</h3>
                <a href="../register/register-propiedad.html">Ir a Registrar</a>

        </div>
    </section>
    <section aria-labelledby="lista-propiedades-heading" style="margin-top: 3rem;">
            <h2 id="lista-propiedades-heading">Mis Propiedades Registradas</h2>

            <div style="overflow-x: auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 1rem;">
                <table id="tabla-propiedades" style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--color-accent);">
                            <th style="padding: 10px;">ID</th>
                            <th style="padding: 10px;">Tipo</th>
                            <th style="padding: 10px;">Sector</th>
                            <th style="padding: 10px;">Precio (CLP)</th>
                            <th style="padding: 10px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="5" style="padding: 10px; text-align: center;">Cargando propiedades...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', cargarPropiedades);

    // 1. Función para obtener e imprimir la lista de propiedades
    async function cargarPropiedades() {
        const tbody = document.querySelector('#tabla-propiedades tbody');
        try {
            const formData = new FormData();
            formData.append('action', 'list');

            // Ajusta la ruta si es necesario
            const response = await fetch('../../BackEnd/api/properties_crud.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                const propiedades = result.data;

                if (propiedades.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="padding: 10px; text-align:center;">No tienes propiedades registradas aún.</td></tr>';
                    return;
                }

                tbody.innerHTML = ''; // Limpiar tabla

                propiedades.forEach(prop => {
                    const tr = document.createElement('tr');
                    tr.style.borderBottom = '1px solid #eee';
                    tr.innerHTML = `
                        <td style="padding: 10px;">#${prop.id}</td>
                        <td style="padding: 10px; text-transform: capitalize;">${prop.tipo}</td>
                        <td style="padding: 10px;">${prop.sector}</td>
                        <td style="padding: 10px;">$${parseInt(prop.precio_clp).toLocaleString('es-CL')}</td>
                        <td style="padding: 10px;">
                            <button onclick="editarPropiedad(${prop.id})" style="background-color: var(--color-accent); margin: 5px; min-width:100px; padding: 0.4rem 0.8rem; font-size: 0.9rem;">
                                Editar
                            </button>
                            <button onclick="eliminarPropiedad(${prop.id})" style="background-color: var(--color-error); margin: 5px; min-width:100px; padding: 0.4rem 0.8rem; font-size: 0.9rem;">
                                Eliminar
                            </button>
                        </td>`;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" style="padding: 10px; text-align:center;">Error al cargar datos.</td></tr>';
            }
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="5" style="padding: 10px; text-align:center;">Error de conexión.</td></tr>';
        }
    }

    // 2. Función para eliminar conectada a tu "case 'delete':"
    async function eliminarPropiedad(id) {
        const confirmacion = await Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción borrará la propiedad y sus fotos. No se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#b91c1c',
            cancelButtonColor: '#1e2d3d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (confirmacion.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            try {
                const response = await fetch('../../BackEnd/api/properties_crud.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    Swal.fire('Eliminada', result.message, 'success');
                    cargarPropiedades(); // Refrescar la tabla sin recargar la página
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        }
    }
    function editarPropiedad(id) {
        window.location.href = `../register/register-propiedad.html?edit=${id}`;
    }
</script>



</main>

<footer role="contentinfo">
    <p>&copy; 2026 PNK Inmobiliaria. Todos los derechos reservados.</p>
</footer>
</body>
</html>
