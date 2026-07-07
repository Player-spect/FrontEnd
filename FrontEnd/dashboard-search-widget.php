<?php /**
 * Componente de búsqueda con autocompletado (no modifica archivos existentes).
 * Instrucciones: incluir este archivo donde desees mostrar el buscador.
 */ ?>
<div id="pnk-search-widget" class="pnk-search-widget">
    <form id="pnk-search-form" role="search" aria-label="Buscar propiedades">
        <div class="pnk-search-input-wrapper">
            <input id="pnk-search-input" name="q" type="search" autocomplete="off" placeholder="Buscar propiedades" aria-label="Buscar propiedades">
            <button id="pnk-search-button" aria-label="Buscar" type="submit">🔍</button>
        </div>
    </form>
    <ul id="pnk-search-suggestions" class="pnk-search-suggestions" role="listbox" aria-label="Sugerencias de búsqueda"></ul>
</div>

<link rel="stylesheet" href="css/search-widget.css">
<script src="js/search-widget.js" defer></script>
