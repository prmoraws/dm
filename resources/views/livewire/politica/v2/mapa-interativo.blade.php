<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">Inteligência territorial</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-900 dark:text-white">Mapa Eleitoral da Bahia</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ number_format($municipiosOficiais, 0, ",", ".") }} municípios oficiais · {{ number_format($municipiosMapeados, 0, ",", ".") }} com coordenadas e acesso ao Espelho Inteligente.</p>
            </div>
            <a href="{{ route('politica.cidades') }}" wire:navigate
                class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                Ver municípios
            </a>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-[1600px] space-y-4 px-3 sm:px-6 lg:px-8">
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5 dark:border-gray-700 dark:bg-gray-800">
                <div class="grid gap-3 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Eleição</label>
                        <select wire:model.live="eleicaoId"
                            class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @forelse ($eleicoes as $eleicao)
                                <option value="{{ $eleicao->id }}">{{ $eleicao->ano }} · {{ $eleicao->turno }}º turno · {{ $eleicao->tipo }}</option>
                            @empty
                                <option value="">Sem eleições</option>
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cargo</label>
                        <select wire:model.live="cargoId"
                            class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @forelse ($cargos as $cargo)
                                <option value="{{ $cargo->id }}">{{ $cargo->nome }}</option>
                            @empty
                                <option value="">Sem cargos</option>
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Candidato</label>
                        <select wire:model.live="candidaturaId"
                            class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">Somente municípios</option>
                            @foreach ($candidaturas as $candidatura)
                                @php($siglaMapa = $candidatura->partido?->sigla ?: config('politica.migracao_v1.partidos_legacy.'.$candidatura->legacy_candidato_id))
                                <option value="{{ $candidatura->id }}">
                                    {{ $candidatura->politico?->nome_publico }}{{ $siglaMapa ? ' · '.$siglaMapa : '' }} · {{ number_format($candidatura->votos_total, 0, ',', '.') }} votos
                                </option>
                            @endforeach
                        </select>
                        @php($limiteMapaCandidaturas = max(25, min((int) config('politica.mapa.max_candidaturas_seletor', 150), 500)))
                        @if ($totalCandidaturas > $limiteMapaCandidaturas)
                            <p class="mt-1 text-[11px] leading-4 text-gray-500 dark:text-gray-400">Exibindo os {{ $limiteMapaCandidaturas }} mais votados neste seletor para manter o mapa leve ({{ number_format($totalCandidaturas, 0, ',', '.') }} candidaturas no recorte).</p>
                        @endif
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-2 border-t border-gray-100 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
                    <div class="min-w-0">
                        @if ($candidaturaSelecionada)
                            <p class="truncate font-semibold text-gray-900 dark:text-white">
                                {{ $candidaturaSelecionada->politico?->nome_publico }}
                                @php($siglaSelecionada = $candidaturaSelecionada->partido?->sigla ?: config('politica.migracao_v1.partidos_legacy.'.$candidaturaSelecionada->legacy_candidato_id))
                                @if ($siglaSelecionada)
                                    <span class="font-normal text-gray-500 dark:text-gray-400">· {{ $siglaSelecionada }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">O tamanho dos pontos representa a força relativa da votação municipal neste recorte.</p>
                        @else
                            <p class="font-medium text-gray-700 dark:text-gray-200">Mapa territorial</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Selecione um candidato para visualizar intensidade de votos por município.</p>
                        @endif
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2 text-xs">
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $municipiosMapeados }} de {{ $municipiosOficiais }} municípios mapeados</span>
                        <a href="{{ route('politica.cidades') }}" wire:navigate
                            class="rounded-full bg-indigo-50 px-2.5 py-1 font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 dark:hover:bg-indigo-900/50">
                            Escolher município e abrir o espelho →
                        </a>
                    </div>
                </div>
            </section>

            @if ($municipiosSemCoordenadas !== [])
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/20 dark:text-amber-200">
                    <span class="font-semibold">Cobertura do mapa:</span>
                    faltam coordenadas para {{ count($municipiosSemCoordenadas) }} município(s): {{ implode(', ', $municipiosSemCoordenadas) }}.
                    Execute <code class="rounded bg-amber-100 px-1 py-0.5 text-xs dark:bg-amber-900/50">php artisan politica:fetch-coordinates</code> para completar automaticamente.
                </div>
            @endif

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div
                    wire:ignore
                    wire:key="politica-map-{{ $eleicaoId ?? 0 }}-{{ $cargoId ?? 0 }}-{{ $candidaturaId ?? 0 }}"
                    x-data
                    x-init="(() => { const boot = () => window.PoliticaMapLoader ? window.PoliticaMapLoader.mount($el, @js($mapaData), { candidato: @js($candidaturaSelecionada?->politico?->nome_publico) }) : setTimeout(boot, 40); boot(); })()"
                    class="politica-map relative w-full bg-gray-100 dark:bg-gray-950"
                    style="height:64vh; min-height:420px"
                    aria-label="Mapa eleitoral interativo da Bahia">
                    <div class="absolute inset-0 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400">Carregando mapa…</div>
                </div>
            </section>

            <p class="px-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                Mapa baseado nas coordenadas armazenadas no sistema. A camada cartográfica usa OpenStreetMap; resultados exibidos vêm exclusivamente da base Política V2 e mantêm sua origem registrada.
            </p>
        </div>
    </div>

    @assets
        <style>
            .politica-map { height: 64vh; min-height: 420px; }
            .politica-map .leaflet-container { font-family: inherit; }
            @media (max-width: 640px) { .politica-map { height: 58vh; min-height: 360px; } }
            @media (min-width: 1024px) { .politica-map { height: 72vh; } }
            .politica-map .leaflet-popup-content-wrapper,
            .politica-map .leaflet-popup-tip { background: #fff; color: #111827; }
            .politica-map .leaflet-control-zoom a { color: #374151; }
            .dark .politica-map .leaflet-tile-pane {
                filter: grayscale(0.75) invert(0.92) hue-rotate(180deg) brightness(0.72) contrast(0.92);
            }
            .dark .politica-map .leaflet-popup-content-wrapper,
            .dark .politica-map .leaflet-popup-tip { background: #111827; color: #f9fafb; }
            .dark .politica-map .leaflet-control-zoom a,
            .dark .politica-map .leaflet-control-attribution { background: #111827; color: #d1d5db; }
            .dark .politica-map .leaflet-control-attribution a { color: #a5b4fc; }
        </style>
    @endassets

    @assets
        <script>
            window.PoliticaMapLoader = window.PoliticaMapLoader || {
                leafletPromise: null,

                loadLeaflet() {
                    if (window.L) return Promise.resolve(window.L);
                    if (this.leafletPromise) return this.leafletPromise;

                    this.leafletPromise = new Promise((resolve, reject) => {
                        if (!document.querySelector('link[data-politica-leaflet]')) {
                            const css = document.createElement('link');
                            css.rel = 'stylesheet';
                            css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                            css.dataset.politicaLeaflet = '1';
                            document.head.appendChild(css);
                        }

                        const existing = document.querySelector('script[data-politica-leaflet]');
                        if (existing) {
                            existing.addEventListener('load', () => resolve(window.L), { once: true });
                            existing.addEventListener('error', reject, { once: true });
                            return;
                        }

                        const script = document.createElement('script');
                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        script.dataset.politicaLeaflet = '1';
                        script.onload = () => resolve(window.L);
                        script.onerror = () => reject(new Error('Não foi possível carregar o Leaflet.'));
                        document.head.appendChild(script);
                    });

                    return this.leafletPromise;
                },

                escapeHtml(value) {
                    return String(value ?? '').replace(/[&<>'"]/g, (char) => ({
                        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
                    }[char]));
                },

                formatNumber(value) {
                    return new Intl.NumberFormat('pt-BR').format(Number(value || 0));
                },

                async mount(element, cidades, meta = {}) {
                    try {
                        const L = await this.loadLeaflet();
                        if (!element.isConnected) return;

                        element.innerHTML = '';
                        const map = L.map(element, {
                            zoomControl: true,
                            preferCanvas: true,
                            minZoom: 5,
                            maxZoom: 14,
                        });

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors',
                            maxZoom: 19,
                        }).addTo(map);

                        const bounds = [];
                        const temCandidato = Boolean(meta.candidato);

                        cidades.forEach((cidade) => {
                            if (!Number.isFinite(Number(cidade.lat)) || !Number.isFinite(Number(cidade.lng))) return;

                            const latLng = [Number(cidade.lat), Number(cidade.lng)];
                            bounds.push(latLng);

                            const intensidade = Number(cidade.intensidade || 0);
                            const votos = Number(cidade.votos || 0);
                            const radius = temCandidato
                                ? (votos > 0 ? 4 + (Math.sqrt(intensidade) * 15) : 2.5)
                                : 4;

                            const marker = L.circleMarker(latLng, {
                                radius,
                                weight: votos > 0 ? 1.5 : 1,
                                color: votos > 0 ? '#4f46e5' : '#6b7280',
                                fillColor: votos > 0 ? '#6366f1' : '#9ca3af',
                                fillOpacity: votos > 0 ? 0.35 + (intensidade * 0.5) : 0.28,
                            }).addTo(map);

                            const nome = this.escapeHtml(cidade.nome);
                            const populacao = cidade.populacao ? this.formatNumber(cidade.populacao) : '—';
                            const votosTexto = temCandidato ? `<div><strong>Votos:</strong> ${this.formatNumber(votos)}</div>` : '';
                            const percentual = cidade.percentual !== null && cidade.percentual !== undefined
                                ? `<div><strong>Percentual:</strong> ${Number(cidade.percentual).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}%</div>`
                                : '';
                            const url = this.escapeHtml(cidade.url);

                            marker.bindPopup(`
                                <div style="min-width:180px">
                                    <div style="font-weight:700;font-size:14px;margin-bottom:6px">${nome}</div>
                                    <div><strong>População:</strong> ${populacao}</div>
                                    ${votosTexto}
                                    ${percentual}
                                    <a href="${url}" style="display:inline-block;margin-top:8px;font-weight:600;color:#4f46e5">Abrir Espelho Inteligente →</a>
                                </div>
                            `);
                        });

                        if (bounds.length) {
                            map.fitBounds(bounds, { padding: [18, 18], maxZoom: 8 });
                        } else {
                            map.setView([-12.6, -41.5], 6);
                        }

                        const resizeObserver = new ResizeObserver(() => map.invalidateSize(false));
                        resizeObserver.observe(element);
                        setTimeout(() => map.invalidateSize(false), 120);

                        element.__politicaMap = map;
                        element.__politicaMapObserver = resizeObserver;
                    } catch (error) {
                        console.error(error);
                        if (element.isConnected) {
                            element.innerHTML = '<div style="display:flex;height:100%;align-items:center;justify-content:center;padding:24px;text-align:center;color:#b91c1c">Não foi possível carregar o mapa. Verifique a conexão com a internet e recarregue a página.</div>';
                        }
                    }
                }
            };
        </script>
    @endassets
</div>
