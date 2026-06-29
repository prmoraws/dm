<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mapa Interativo da Bahia
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div wire:ignore id="map" style="height: 70vh; z-index: 1;"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Adiciona um listener para garantir que o script só rode depois que a página inteira carregar
        document.addEventListener('livewire:navigated', () => {
            // 'L' já está disponível globalmente a partir do app.js
            // Verifica se o mapa já foi inicializado para evitar erros de navegação
            if (typeof L === 'undefined' || document.getElementById('map')._leaflet_id) {
                return;
            }
            
            const cities = JSON.parse(@json($citiesJson));
            const map = L.map('map').setView([-12.97, -38.50], 7);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            cities.forEach(city => {
                if (city.latitude && city.longitude) {
                    const marker = L.marker([city.latitude, city.longitude]).addTo(map);
                    marker.bindPopup(`<b>${city.nome}</b><br>População: ${new Intl.NumberFormat('pt-BR').format(city.populacao)}`);
                }
            });
        });
    </script>
    @endpush
</div>