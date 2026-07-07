<?php require_once '../BackEnd/includes/navegacion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNK Inmobiliaria - Detalle de Propiedad</title>
    <meta name="description" content="Detalle de la propiedad seleccionada en La Serena con sector, precio e imagen.">
    <meta name="keywords" content="detalle propiedad, PNK Inmobiliaria, propiedad La Serena, precio sector descripcion">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styles.css">
    
    <style>
        .property-detail-carousel {
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }
        .property-detail-carousel .carousel-item {
            height: 500px; /* Altura de las fotos en el detalle */
        }
        .property-detail-carousel img {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }
        /* Sombras para que se vean bien las flechas */
        .property-detail-carousel .carousel-inner::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(0,0,0,0.15) 0%, transparent 15%, transparent 85%, rgba(0,0,0,0.15) 100%);
            pointer-events: none;
        }
    </style>
</head>
<body>
<a href="#main-content" class="skip-link">Saltar al contenido principal</a>

<main id="main-content" role="main" class="container mt-4">
    <section aria-labelledby="detail-heading">
        <h2 id="detail-heading" class="mb-4">Detalle de Propiedad</h2>

        <div id="property-detail-card" class="property-detail-card hide" aria-live="polite">
            
            <div id="propertyCarousel" class="carousel slide property-detail-carousel" data-bs-ride="false">
                <div class="carousel-inner" id="carousel-inner-container">
                    </div>
                
                <button class="carousel-control-prev d-none" id="carousel-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 20px;"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next d-none" id="carousel-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 20px;"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
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
                <p class="property-description mt-3" id="detail-description"></p>
                <p class="property-detail-actions mt-4">
                    <a id="detail-back" href="login.php" class="btn btn-primary">Agendar una visita</a>
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

<footer role="contentinfo" class="mt-5 text-center">
    <p>&copy; 2026 PNK Inmobiliaria. Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
 document.addEventListener('DOMContentLoaded', async function() {
        function getQueryParam(name) {
            return new URLSearchParams(window.location.search).get(name);
        }

        const propertyId = getQueryParam('property');
        const detailCard = document.getElementById('property-detail-card');
        const notFound = document.getElementById('property-not-found');

        if (!propertyId) {
            notFound.classList.remove('hide');
            return;
        }

        try {
            // CORRECCIÓN: Comillas invertidas añadidas
            const response = await fetch(`../../BackEnd/api/get_property_detail.php?id=${propertyId}`);
            const result = await response.json();

            if (response.ok && result.success) {
                const prop = result.data;

                // CORRECCIÓN: Comillas invertidas añadidas
                const imagenRuta = prop.main_image
                    ? `../uploads/propiedades/${prop.main_image}`
                    : '../assets/img/casa-serena.jpg';

                const fecha = new Date(prop.fecha_publicacion);
                const fechaFormat = !isNaN(fecha.getTime()) ? fecha.toLocaleDateString('es-CL') : prop.fecha_publicacion;

                // ==========================================
                // LÓGICA DEL CARRUSEL DE IMÁGENES
                // ==========================================
                const carouselInner = document.getElementById('carousel-inner-container');
                const btnPrev = document.getElementById('carousel-prev');
                const btnNext = document.getElementById('carousel-next');

                // 1. Agregar la imagen principal (siempre la primera y activa)
                // CORRECCIÓN: Comillas invertidas añadidas
                carouselInner.innerHTML = `
                    <div class="carousel-item active">
                        <img src="${imagenRuta}" alt="${prop.tipo} en ${prop.sector}" class="d-block w-100" loading="lazy">
                    </div>
                `;

                // 2. Buscar si hay imágenes extras en la base de datos
                try {
                    // CORRECCIÓN: Comillas invertidas añadidas
                    const imgResponse = await fetch(`../../BackEnd/api/get_property_images.php?id=${propertyId}`);
                    const imgResult = await imgResponse.json();

                    if (imgResponse.ok && imgResult.success && imgResult.images.length > 0) {
                        // Agregamos cada foto extra como un slide nuevo
                        imgResult.images.forEach(imageSrc => {
                            const item = document.createElement('div');
                            item.className = 'carousel-item';
                            // CORRECCIÓN: Comillas invertidas añadidas
                            item.innerHTML = `<img src="../uploads/propiedades/${imageSrc}" class="d-block w-100" alt="Foto adicional" loading="lazy">`;
                            carouselInner.appendChild(item);
                        });

                        // Como hay más de 1 foto, mostramos las flechitas
                        btnPrev.classList.remove('d-none');
                        btnNext.classList.remove('d-none');
                    }
                } catch (imgError) {
                    console.error('Error cargando imágenes extras:', imgError);
                }

                // ==========================================
                // LÓGICA DE TEXTOS Y DETALLES
                // ==========================================
                // CORRECCIÓN: Comillas invertidas añadidas a todas las inyecciones de texto
                document.getElementById('detail-title').textContent = `${prop.tipo.toUpperCase()} - ${prop.sector}`;
                document.getElementById('detail-sector').textContent = prop.sector;
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

                document.getElementById('detail-description').textContent = prop.descripcion;

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
