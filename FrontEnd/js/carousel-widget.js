window.initCarousels = async function () {
  const carousels = document.querySelectorAll('.carousel.slide');
  if (!carousels.length) return;

  async function fetchPropertyImages(propertyId) {
    try {
      const response = await fetch(`../BackEnd/api/get_property_images.php?id=${propertyId}`);
      const json = await response.json();
      if (!response.ok || !json.success || !Array.isArray(json.images)) return [];
      return json.images;
    } catch (error) {
      console.error('Error fetching property images', error);
      return [];
    }
  }

  for (const carousel of carousels) {
    const propertyId = carousel.id.replace('carouselProp', '');
    if (!propertyId) continue;

    const images = await fetchPropertyImages(propertyId);

    if (images.length > 0) {
      const innerContainer = document.getElementById(`inner-${propertyId}`);
      const btnPrev = document.getElementById(`prev-${propertyId}`);
      const btnNext = document.getElementById(`next-${propertyId}`);

      images.forEach((imageSrc) => {
        const item = document.createElement('div');
        item.className = 'carousel-item';
        item.style.height = '100%';
        item.innerHTML = `
            <a href="FrontEnd/property-detail.php?property=${propertyId}">
                <img src="../uploads/propiedades/${imageSrc}" class="d-block w-100" style="object-fit: cover; height: 100%;" alt="Foto adicional" loading="lazy">
            </a>
        `;
        innerContainer.appendChild(item);
      });

      if (btnPrev) btnPrev.classList.remove('d-none');
      if (btnNext) btnNext.classList.remove('d-none');
    }
  }
};
