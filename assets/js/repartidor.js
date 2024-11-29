// Inicializar el mapa centrado
const map = L.map('map').setView([40.7128, -74.0060], 5);

// Añadir capa de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

let routingControl;

// Función para trazar la ruta
function drawRoute() {
    const startInput = document.getElementById("start").value;
    const endInput = document.getElementById("end").value;

    const [startLat, startLon] = startInput.split(",").map(coord => parseFloat(coord.trim()));
    const [endLat, endLon] = endInput.split(",").map(coord => parseFloat(coord.trim()));

    // Remover la ruta previa, si existe
    if (routingControl) {
        map.removeControl(routingControl);
    }

    // Crear una nueva ruta
    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(startLat, startLon),
            L.latLng(endLat, endLon)
        ],
        router: L.Routing.osrmv1({ serviceUrl: 'https://router.project-osrm.org/route/v1/' }),
        lineOptions: { styles: [{ color: 'blue', opacity: 0.7, weight: 5 }] },
        createMarker: function () { return null; } // No mostrar marcadores en los puntos de inicio y fin
    }).addTo(map);
}

// Función para obtener la ubicación actual
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            document.getElementById("start").value = `${lat.toFixed(4)}, ${lon.toFixed(4)}`;
        }, () => {
            alert("No se pudo obtener la ubicación.");
        });
    } else {
        alert("La geolocalización no es compatible con este navegador.");
    }
}



// Función para abrir Google Maps con la ruta desde 'start' hasta 'end'
function openGoogleMaps() {
    // Obtén las coordenadas de inicio y destino
    var start = document.getElementById("start").value;
    var end = document.getElementById("end").value;

    // Construir la URL de Google Maps con las coordenadas de inicio y destino
    var url = "https://www.google.com/maps/dir/?api=1&origin=" + start + "&destination=" + end;

    // Redirige al usuario a la URL
    window.location.href = url;
}
