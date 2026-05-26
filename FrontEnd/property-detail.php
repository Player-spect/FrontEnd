<?php require_once '../BackEnd/includes/navegacion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNK Inmobiliaria - Detalle de Propiedad</title>
    <meta name="description" content="Detalle de la propiedad seleccionada en La Serena con sector, precio e imagen.">
    <meta name="keywords" content="detalle propiedad, PNK Inmobiliaria, propiedad La Serena, precio sector descripcion">
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
    <section aria-labelledby="detail-heading">
        <h2 id="detail-heading">Detalle de Propiedad</h2>

        <div id="property-detail-card" class="property-detail-card hide" aria-live="polite">
            <img id="detail-image" src="" alt="">
            <div class="property-detail-content">
                <h3 class="property-detail-title" id="detail-title"></h3>
                <div class="property-meta">
                    <span id="detail-sector"></span>
                    <span id="detail-price"></span>
                    <span id="detail-type"></span>
                    <span id="detail-bedrooms"></span>
                    <span id="detail-bathrooms"></span>
                    <span id="detail-area"></span>
                    <span id="detail-built-area"></span>
                    <span id="detail-uf"></span>
                    <span id="detail-publication-date"></span>
                    <span id="detail-bodega"></span>
                    <span id="detail-estacionamiento"></span>
                    <span id="detail-logia"></span>
                    <span id="detail-cocina"></span>
                    <span id="detail-antejardin"></span>
                    <span id="detail-patio"></span>
                    <span id="detail-piscina"></span>
                </div>
                <p class="property-description" id="detail-description"></p>
                <p class="property-detail-actions">
                    <a id="detail-back" href="login.php">Agendar una visita</a>
                </p>
            </div>
        </div>

        <div id="property-not-found" class="property-detail-card hide">
            <h3>Propiedad no encontrada</h3>
            <p>La propiedad solicitada no está disponible. Vuelve al inicio para seleccionar otra opción.</p>
            <p><a href="../index.html">Volver al inicio</a></p>
        </div>
    </section>
</main>

<footer role="contentinfo">
    <p>&copy; 2026 PNK Inmobiliaria. Todos los derechos reservados.</p>
</footer>

<script>
 document.addEventListener('DOMContentLoaded', async function() {
        function getQueryParam(name) {
            return new URLSearchParams(window.location.search).get(name);
        }

        const propertyId = getQueryParam('property');
        const detailCard = document.getElementById('property-detail-card');
        const notFound = document.getElementById('property-not-found');

        // Si alguien entra sin ID en la URL, mostramos error
        if (!propertyId) {
            notFound.classList.remove('hide');
            return;
        }

        try {
            // Hacemos la consulta a nuestro nuevo endpoint
            const response = await fetch(`../../BackEnd/api/get_property_detail.php?id=${propertyId}`);
            const result = await response.json();

            if (response.ok && result.success) {
                const prop = result.data;

                // Configurar ruta de imagen
                const imagenRuta = prop.main_image
                    ? `uploads/propiedades/${prop.main_image}`
                    : '../assets/img/casa-serena.jpg';

                // Formatear la fecha
                const fecha = new Date(prop.fecha_publicacion);
                const fechaFormat = !isNaN(fecha.getTime()) ? fecha.toLocaleDateString('es-CL') : prop.fecha_publicacion;

                // Inyectar datos en el DOM
                document.getElementById('detail-image').src = imagenRuta;
                document.getElementById('detail-image').alt = `${prop.tipo} en ${prop.sector}`;
                document.getElementById('detail-title').textContent = `${prop.tipo.toUpperCase()} - ${prop.sector}`;
                document.getElementById('detail-sector').textContent = prop.sector;

                // Precios y medidas
                document.getElementById('detail-price').textContent = `CLP $${parseInt(prop.precio_clp).toLocaleString('es-CL')}`;
                document.getElementById('detail-uf').textContent = `UF ${parseInt(prop.precio_uf).toLocaleString('es-CL')}`;
                document.getElementById('detail-type').textContent = prop.tipo;
                document.getElementById('detail-bedrooms').textContent = `${prop.dormitorios} dormitorios`;
                document.getElementById('detail-bathrooms').textContent = `${prop.banos} baños`;
                document.getElementById('detail-area').textContent = `${parseInt(prop.area_terreno)} m² terreno`;
                document.getElementById('detail-built-area').textContent = `${parseInt(prop.area_construida)} m² construidos`;
                document.getElementById('detail-publication-date').textContent = `Publicado: ${fechaFormat}`;

                // Características booleanas
                document.getElementById('detail-bodega').textContent = prop.bodega ? 'Bodega' : '';
                document.getElementById('detail-estacionamiento').textContent = prop.estacionamiento ? 'Estacionamiento' : '';
                document.getElementById('detail-logia').textContent = prop.logia ? 'Logia' : '';
                document.getElementById('detail-cocina').textContent = prop.cocina_amoblada ? 'Cocina amoblada' : '';
                document.getElementById('detail-antejardin').textContent = prop.antejardin ? 'Antejardín' : '';
                document.getElementById('detail-patio').textContent = prop.patio_trasero ? 'Patio trasero' : '';
                document.getElementById('detail-piscina').textContent = prop.piscina ? 'Piscina' : '';

                // Descripción larga
                document.getElementById('detail-description').textContent = prop.descripcion;

                // Mostrar la tarjeta
                detailCard.classList.remove('hide');
            } else {
                notFound.classList.remove('hide');
            }
        } catch (error) {
            notFound.classList.remove('hide');
            console.error('Error cargando los detalles de la propiedad:', error);
        }
    });
</script>
</body>
</html>
