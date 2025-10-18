// Google Maps integration
// Expects an API key available at window.GOOGLE_MAPS_API_KEY (set it in a script tag in the HTML
// or edit the placeholder below). If not set, the map will load but API calls may fail.

const GM_API_KEY = window.GOOGLE_MAPS_API_KEY || 'YOUR_GOOGLE_MAPS_API_KEY';

function loadGoogleMaps(callback) {
    if (window.google && window.google.maps) return callback();
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${GM_API_KEY}&libraries=places`;
    script.defer = true;
    script.onload = () => callback();
    script.onerror = () => console.error('Failed to load Google Maps script.');
    document.head.appendChild(script);
}

function initGoogleMap() {
    const mapEl = document.getElementById('map');
    if (!mapEl || !window.google || !window.google.maps) return;

    // sensible default: center on a neutral location if geolocation denied
    const defaultPos = { lat: 4.859363, lng: 31.571251 };
    const map = new google.maps.Map(mapEl, { center: defaultPos, zoom: 13 });

    const marker = new google.maps.Marker({ position: defaultPos, map, draggable: true, title: 'Drag to set delivery location' });

    function updateHiddenInputs(lat, lng) {
        const latEl = document.getElementById('latitude');
        const lngEl = document.getElementById('longitude');
        const coordsEl = document.getElementById('coords');
        if (latEl) latEl.value = lat;
        if (lngEl) lngEl.value = lng;
        if (coordsEl) coordsEl.textContent = `Lat: ${Number(lat).toFixed(6)}, Lng: ${Number(lng).toFixed(6)}`;
    }

    // initialize inputs
    updateHiddenInputs(defaultPos.lat, defaultPos.lng);

    // When marker dragged
    marker.addListener('dragend', () => {
        const pos = marker.getPosition();
        updateHiddenInputs(pos.lat(), pos.lng());
    });

    // When user clicks map, move marker
    map.addListener('click', (e) => {
        marker.setPosition(e.latLng);
        updateHiddenInputs(e.latLng.lat(), e.latLng.lng());
    });

    // locate button
    const locateBtn = document.getElementById('locate-btn');
    if (locateBtn) {
        locateBtn.addEventListener('click', () => {
            if (navigator.geolocation) {
                locateBtn.disabled = true;
                locateBtn.textContent = 'Locating...';
                navigator.geolocation.getCurrentPosition((position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const latLng = new google.maps.LatLng(lat, lng);
                    map.setCenter(latLng);
                    map.setZoom(15);
                    marker.setPosition(latLng);
                    updateHiddenInputs(lat, lng);
                    locateBtn.disabled = false;
                    locateBtn.textContent = 'Use my location';
                }, (err) => {
                    console.error('Geolocation error', err);
                    if (window.showToast) window.showToast('Could not get location. Please allow location access.', false);
                    locateBtn.disabled = false;
                    locateBtn.textContent = 'Use my location';
                }, { timeout: 10000 });
            } else {
                if (window.showToast) window.showToast('Geolocation not supported in your browser.', false);
            }
        });
    }

    // reset button returns to default position
    const resetBtn = document.getElementById('reset-marker-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            map.setCenter(defaultPos);
            map.setZoom(13);
            marker.setPosition(defaultPos);
            updateHiddenInputs(defaultPos.lat, defaultPos.lng);
        });
    }
}

// Initialize Google Maps on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    loadGoogleMaps(initGoogleMap);
});

document.addEventListener('DOMContentLoaded', () => {
    // --- Payment Gateway Logic ---
    const paymentOptions = document.getElementById('payment-options');
    const cardDetails = document.getElementById('card-details');
    const mobileMoneyDetails = document.getElementById('mobile-money-details');

    if (paymentOptions) {
        paymentOptions.addEventListener('change', (e) => {
            if (e.target.name === 'payment') {
                const selectedPayment = e.target.value;
                cardDetails.classList.toggle('hidden', selectedPayment !== 'card');
                mobileMoneyDetails.classList.toggle('hidden', selectedPayment !== 'mobile');
            }
        });
    }

    // --- Fuel Order Form Submission ---
    const fuelOrderForm = document.getElementById('fuelOrderForm');
    if (fuelOrderForm) {
        fuelOrderForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            // collect form data into an object
            const fd = new FormData(form);
            const data = {};
            for (const [k, v] of fd.entries()) {
                data[k] = v;
            }

            try {
                const resp = await fetch('/api/orders.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const json = await resp.json().catch(() => null);
                if (resp.ok && json && json.success) {
                    if (window.showToast) window.showToast('Fuel order placed successfully!');
                    form.reset();
                    if(cardDetails) cardDetails.classList.add('hidden');
                    if(mobileMoneyDetails) mobileMoneyDetails.classList.add('hidden');
                } else {
                    const msg = (json && json.error) ? json.error : 'Failed to place order';
                    if (window.showToast) window.showToast(msg, false);
                }
            } catch (err) {
                if (window.showToast) window.showToast('Network error: could not reach API', false);
                console.error('Order submit error', err);
            }
        });
    }
});
