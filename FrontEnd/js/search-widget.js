(() => {
  const input = document.getElementById('pnk-search-input');
  const suggestions = document.getElementById('pnk-search-suggestions');
  const form = document.getElementById('pnk-search-form');
  let activeIndex = -1;

  function debounce(fn, wait) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
  }

  async function fetchSuggestions(q) {
    if (!q || q.trim() === '') { suggestions.innerHTML = ''; suggestions.style.display = 'none'; return; }
    const url = new URL('../BackEnd/api/search_property_live.php', window.location.href);
    url.searchParams.set('provincia', q);
    try {
      const res = await fetch(url.toString(), { cache: 'no-store' });
      const data = await res.json();
      if (data && data.success && Array.isArray(data.results)) {
        renderSuggestions(data.results);
      } else {
        suggestions.innerHTML = '';
        suggestions.style.display = 'none';
      }
    } catch (err) {
      console.error('Error buscando propiedades', err);
      suggestions.innerHTML = '';
      suggestions.style.display = 'none';
    }
  }

  function highlightMatch(text, q) {
    if (!q) return text;
    const idx = text.toLowerCase().indexOf(q.toLowerCase());
    if (idx === -1) return text;
    return text.slice(0, idx) + '<strong>' + text.slice(idx, idx + q.length) + '</strong>' + text.slice(idx + q.length);
  }

  function renderSuggestions(items) {
    suggestions.innerHTML = '';
    if (!items.length) { suggestions.style.display = 'none'; return; }
    items.forEach((it, i) => {
      const li = document.createElement('li');
      li.setAttribute('role', 'option');
      li.dataset.id = it.id;
      const icon = document.createElement('span');
      icon.className = 'pnk-suggestion-icon';
      icon.textContent = '🔎';
      const main = document.createElement('span');
      main.className = 'pnk-suggestion-main';
      main.innerHTML = highlightMatch((it.descripcion || it.tipo || 'Propiedad'), input.value);
      const sub = document.createElement('span');
      sub.className = 'pnk-suggestion-sub';
      sub.textContent = [it.provincia, it.comuna, it.sector].filter(Boolean).join(' · ');
      li.appendChild(icon);
      const textWrap = document.createElement('div');
      textWrap.appendChild(main);
      if (sub.textContent) textWrap.appendChild(sub);
      li.appendChild(textWrap);
      li.addEventListener('click', () => selectSuggestion(it));
      suggestions.appendChild(li);
    });
    suggestions.style.display = 'block';
    activeIndex = -1;
  }

  function selectSuggestion(item) {
    // Intentar ir a la página de detalle (ruta relativa usada en dashboard)
    const target = '../FrontEnd/property-detail.php?property=' + encodeURIComponent(item.id);
    window.location.href = target;
  }

  const debouncedFetch = debounce((q) => fetchSuggestions(q), 250);

  input.addEventListener('input', (e) => debouncedFetch(e.target.value));

  input.addEventListener('keydown', (e) => {
    const list = suggestions.querySelectorAll('li');
    if (!list.length) return;
    if (e.key === 'ArrowDown') {
      e.preventDefault(); activeIndex = Math.min(activeIndex + 1, list.length -1); updateActive(list);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault(); activeIndex = Math.max(activeIndex -1, 0); updateActive(list);
    } else if (e.key === 'Enter') {
      e.preventDefault(); if (activeIndex >=0 && list[activeIndex]) list[activeIndex].click();
    } else if (e.key === 'Escape') {
      suggestions.innerHTML = ''; suggestions.style.display = 'none';
    }
  });

  function updateActive(list) {
    list.forEach((li, idx) => li.classList.toggle('active', idx === activeIndex));
    if (activeIndex >=0) {
      const el = list[activeIndex]; el.scrollIntoView({block:'nearest'});
    }
  }

  document.addEventListener('click', (e) => {
    if (!document.getElementById('pnk-search-widget').contains(e.target)) {
      suggestions.innerHTML = '';
      suggestions.style.display = 'none';
    }
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault(); const q = input.value.trim(); if (!q) return;
    // Buscar con la API y si hay resultados, ir al primero
    fetchSuggestions(q).then(() => {
      const first = suggestions.querySelector('li');
      if (first) first.click();
    });
  });

})();
