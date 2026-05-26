<?php
require_once '../../BackEnd/includes/session_checker.php';
if ($_SESSION['user_rol'] !== 'admin') { header('Location: ../dashboard-gestor.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador - PNK Inmobiliaria</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php require_once '../../BackEnd/includes/navegacion.php'; ?>

<main id="main-content">
    <section>
        <h2>Administración General</h2>
        <div class="dashboard">
            <article class="card"><button onclick="cargarTabla('usuarios')">Gestionar Usuarios</button></article>
            <article class="card"><button onclick="cargarTabla('propiedades')">Gestionar Propiedades</button></article>
        </div>
    </section>

    <section id="data-section" style="margin-top: 3rem; display: none;">
        <h2 id="table-title">Datos del Sistema</h2>
        <table id="data-table" style="width: 100%; border-collapse: collapse;">
            <thead><tr id="table-head"></tr></thead>
            <tbody id="table-body"></tbody>
        </table>
    </section>
</main>

<script>
    let tipoActual = '';

    async function cargarTabla(tipo) {
        tipoActual = tipo;
        const tbody = document.getElementById('table-body');
        const thead = document.getElementById('table-head');
        document.getElementById('data-section').style.display = 'block';

        const response = await fetch(`../../BackEnd/api/admin_crud.php?action=list_${tipo}`);
        const result = await response.json();

        if (result.success) {
            const columnas = Object.keys(result.data[0]);
            thead.innerHTML = columnas.map(c => `<th>${c.toUpperCase()}</th>`).join('') + '<th>ACCIONES</th>';
            tbody.innerHTML = result.data.map(item => `
                <tr>
                    ${Object.values(item).map(val => `<td>${val}</td>`).join('')}
                    <td>
                        ${tipo === 'usuarios' ? `
                            <select onchange="cambiarEstado(${item.id}, this.value)">
                                <option value="activo" ${item.estado === 'activo' ? 'selected' : ''}>Activo</option>
                                <option value="inactivo" ${item.estado === 'inactivo' ? 'selected' : ''}>Inactivo</option>
                            </select>
                            <button onclick="ejecutarAccion('delete_propiedad', ${item.id})" style="background-color: var(--color-error); margin-center; min-width:100px; padding: 0.4rem 0.8rem; font-size: 0.9rem;">
                               Eliminar
                        ` : `
                            <button onclick="ejecutarAccion('delete_propiedad', ${item.id})" style="background-color: var(--color-error); margin: 5px; min-width:100px; padding: 0.4rem 0.8rem; font-size: 0.9rem;">
                                Eliminar
                            </button>
                        `}
                    </td>
                </tr>
            `).join('');
        }
    }

    async function cambiarEstado(id, nuevoEstado) {
        const fd = new FormData();
        fd.append('action', 'update_estado');
        fd.append('id', id);
        fd.append('estado', nuevoEstado);
        await fetch('../../BackEnd/api/admin_crud.php', { method: 'POST', body: fd });
        Swal.fire('Éxito', 'Estado actualizado', 'success');
    }

    async function ejecutarAccion(accion, id) {
        if ((await Swal.fire({title: '¿Seguro?', icon: 'warning', showCancelButton: true})).isConfirmed) {
            const fd = new FormData();
            fd.append('action', accion);
            fd.append('id', id);
            await fetch('../../BackEnd/api/admin_crud.php', { method: 'POST', body: fd });
            cargarTabla(tipoActual);
        }
    }
</script>
</body>
</html>
