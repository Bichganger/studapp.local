/**
 * Студенческий компас - Интерактивная карта Leaflet
 * Калининград, корпуса и локации для студентов
 */

document.addEventListener('DOMContentLoaded', function() {
    // Данные из PHP
    const data = window.mapData || { locations: [], campuses: [], categories: {} };
    
    // Центр Калининграда
    const KALININGRAD_CENTER = [54.7104, 20.4522];
    const DEFAULT_ZOOM = 13;
    
    // Инициализация карты
    const map = L.map('map', {
        zoomControl: false,
        attributionControl: false
    }).setView(KALININGRAD_CENTER, DEFAULT_ZOOM);
    
    // Добавляем контрол зума справа
    L.control.zoom({ position: 'topright' }).addTo(map);
    
    // Тёмная тема карты (CartoDB Dark Matter)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);
    
    // Группы маркеров по категориям
    const markerGroups = {};
    const allMarkers = [];
    
    // Создаём кастомные иконки
    function createCustomIcon(color, iconClass) {
        return L.divIcon({
            className: 'custom-marker',
            html: `<div style="
                background: ${color};
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 3px solid #0a0e27;
                box-shadow: 0 2px 10px ${color}80;
                color: white;
                font-size: 14px;
            "><i class="bi ${iconClass}"></i></div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -20]
        });
    }
    
    function createCampusIcon(color) {
        return L.divIcon({
            className: 'custom-marker',
            html: `<div style="
                background: ${color};
                width: 48px;
                height: 48px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 4px solid #0a0e27;
                box-shadow: 0 4px 20px ${color}80;
                color: white;
                font-size: 20px;
                font-weight: bold;
            "><i class="bi bi-building"></i></div>`,
            iconSize: [48, 48],
            iconAnchor: [24, 24],
            popupAnchor: [0, -28]
        });
    }
    
    // Добавляем корпуса
    data.campuses.forEach(campus => {
        const marker = L.marker([campus.lat, campus.lng], {
            icon: createCampusIcon(campus.color),
            zIndexOffset: 1000
        }).addTo(map);
        
        marker.bindPopup(`
            <div style="min-width: 200px;">
                <h6 style="margin: 0 0 8px 0; color: ${campus.color}; font-weight: 700;">
                    <i class="bi bi-building me-1"></i>${campus.name}
                </h6>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">
                    Учебный корпус
                </p>
            </div>
        `);
        
        allMarkers.push(marker);
    });
    
    // Добавляем локации
    data.locations.forEach(loc => {
        const cat = data.categories[loc.category];
        if (!cat) return;
        
        const marker = L.marker([parseFloat(loc.latitude), parseFloat(loc.longitude)], {
            icon: createCustomIcon(cat.color, cat.icon)
        });
        
        // Создаём popup
        const popupContent = `
            <div style="min-width: 220px;">
                <h6 style="margin: 0 0 8px 0; color: ${cat.color}; font-weight: 700;">
                    <i class="bi ${cat.icon} me-1"></i>${loc.name}
                </h6>
                <p style="margin: 0 0 6px 0; color: var(--text-secondary); font-size: 0.85rem;">
                    <i class="bi bi-geo-alt me-1"></i>${loc.address}
                </p>
                ${loc.working_hours ? `
                <p style="margin: 0 0 6px 0; color: var(--text-muted); font-size: 0.8rem;">
                    <i class="bi bi-clock me-1"></i>${loc.working_hours}
                </p>
                ` : ''}
                ${loc.graduate_comment ? `
                <div style="
                    background: rgba(124, 77, 255, 0.1);
                    border-left: 3px solid var(--accent-purple);
                    padding: 8px 10px;
                    border-radius: 0 6px 6px 0;
                    margin-top: 8px;
                    font-size: 0.8rem;
                    color: var(--text-secondary);
                    line-height: 1.4;
                ">
                    <i class="bi bi-chat-quote me-1" style="color: var(--accent-purple);"></i>
                    ${loc.graduate_comment}
                </div>
                ` : ''}
                <p style="margin: 6px 0 0 0; color: var(--text-muted); font-size: 0.75rem;">
                    <i class="bi bi-buildings me-1"></i>Рядом: ${loc.campus_near}
                </p>
            </div>
        `;
        
        marker.bindPopup(popupContent);
        
        // Добавляем в группу
        if (!markerGroups[loc.category]) {
            markerGroups[loc.category] = [];
        }
        markerGroups[loc.category].push(marker);
        allMarkers.push(marker);
    });
    
    // Добавляем все маркеры на карту
    Object.values(markerGroups).forEach(group => {
        group.forEach(marker => marker.addTo(map));
    });
    
    // Обработка чекбоксов категорий
    const categoryToggles = document.querySelectorAll('.category-toggle');
    categoryToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const category = this.value;
            const group = markerGroups[category];
            
            if (group) {
                if (this.checked) {
                    group.forEach(marker => marker.addTo(map));
                } else {
                    group.forEach(marker => map.removeLayer(marker));
                }
            }
        });
    });
    
    // Кнопки перехода к корпусам
    const campusButtons = document.querySelectorAll('.fly-to-campus');
    campusButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const lat = parseFloat(this.dataset.lat);
            const lng = parseFloat(this.dataset.lng);
            map.flyTo([lat, lng], 16, {
                duration: 1.5
            });
        });
    });
    
    // Кнопка сброса карты
    const resetBtn = document.getElementById('resetMap');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            map.flyTo(KALININGRAD_CENTER, DEFAULT_ZOOM, {
                duration: 1.5
            });
        });
    }
    
    // Фит карты по всем маркерам при первой загрузке
    if (allMarkers.length > 0) {
        const group = new L.featureGroup(allMarkers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
});
