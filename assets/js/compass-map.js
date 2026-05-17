/**
 * Студенческий компас — Карта Калининграда (Leaflet)
 */
document.addEventListener('DOMContentLoaded', function () {
    const data = window.mapData || { locations: [], campuses: [], categories: {} };
    
    // Центр Калининграда (район Брамса/Спортивной/Озерова)
    const CENTER = [54.7150, 20.4885];
    const DEFAULT_ZOOM = 15;

    const map = L.map('map', { zoomControl: false, attributionControl: false })
        .setView(CENTER, DEFAULT_ZOOM);

    L.control.zoom({ position: 'topright' }).addTo(map);
    
    // Тёмная подложка карты (OSM + CartoDB)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19,
        errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
    }).addTo(map);
    
    const markerGroups = {};
    const allMarkers = [];
    
    // Иконка POI
    function poiIcon(color, iconClass) {
        return L.divIcon({
            className: 'custom-marker',
            html: `<div style="background:${color};width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid #0a0e27;box-shadow:0 2px 10px ${color}80;color:#fff;font-size:14px;"><i class="bi ${iconClass}"></i></div>`,
            iconSize: [34, 34],
            iconAnchor: [17, 17],
            popupAnchor: [0, -19]
        });
    }

    // Иконка корпуса
    function campusIcon(color) {
        return L.divIcon({
            className: 'custom-marker',
            html: `<div style="background:${color};width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:4px solid #0a0e27;box-shadow:0 4px 20px ${color}80;color:#fff;font-size:20px;"><i class="bi bi-building"></i></div>`,
            iconSize: [46, 46],
            iconAnchor: [23, 23],
            popupAnchor: [0, -26]
        });
    }

    // Корпуса
    data.campuses.forEach(c => {
        const m = L.marker([c.lat, c.lng], { icon: campusIcon(c.color), zIndexOffset: 1000 }).addTo(map);
        m.bindPopup(`<div style="min-width:200px;color:#e0e4ff;"><h6 style="color:${c.color};margin:0 0 6px;"><i class="bi bi-building me-1"></i>${c.name}</h6><p style="margin:0;color:#a0a8cc;font-size:0.9rem;"><i class="bi bi-geo-alt me-1"></i>${c.address}</p></div>`);
        allMarkers.push(m);
    });

    // POI локации
    data.locations.forEach(loc => {
        const cat = data.categories[loc.type];
        if (!cat) return;
        const m = L.marker([loc.lat, loc.lng], { icon: poiIcon(cat.color, cat.icon) });
        let html = `<div style="min-width:210px;color:#e0e4ff;"><h6 style="color:${cat.color};margin:0 0 6px;"><i class="bi ${cat.icon} me-1"></i>${loc.name}</h6>`;
        if (loc.address) html += `<p style="margin:0 0 4px;color:#a0a8cc;font-size:0.85rem;"><i class="bi bi-geo-alt me-1"></i>${loc.address}</p>`;
        if (loc.hours) html += `<p style="margin:0;color:#5a6380;font-size:0.8rem;"><i class="bi bi-clock me-1"></i>${loc.hours}</p>`;
        html += `</div>`;
        m.bindPopup(html);
        if (!markerGroups[loc.type]) markerGroups[loc.type] = [];
        markerGroups[loc.type].push(m);
        allMarkers.push(m);
    });
        
    // Показать все маркеры
    Object.values(markerGroups).forEach(g => g.forEach(m => m.addTo(map)));

    // Фильтрация по категориям
    document.querySelectorAll('.category-toggle').forEach(tog => {
        tog.addEventListener('change', function () {
            const group = markerGroups[this.value];
            if (!group) return;
            group.forEach(m => this.checked ? m.addTo(map) : map.removeLayer(m));
        });
    });
        
    // Полёт к корпусу
    document.querySelectorAll('.fly-to-campus').forEach(btn => {
        btn.addEventListener('click', function () {
            map.flyTo([parseFloat(this.dataset.lat), parseFloat(this.dataset.lng)], 17, { duration: 1.2 });
        });
    });

    // Сброс
    const resetBtn = document.getElementById('resetMap');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => map.flyTo(CENTER, DEFAULT_ZOOM, { duration: 1.2 }));
    }
});
