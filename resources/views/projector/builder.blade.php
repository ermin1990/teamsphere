@extends('layouts.public')

@section('title', 'Projektor Builder - MojTurnir')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl md:text-5xl font-extrabold mb-3 tracking-tight" style="color: var(--text-primary); text-shadow: 0 0 20px rgba(96, 165, 250, 0.3);">
            🎬 Projektor Builder
        </h1>
        <p class="text-lg md:text-xl font-medium" style="color: var(--text-secondary);">
            Kreirajte profesionalni prenos ili prikaz za velike ekrane
        </p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/50 backdrop-blur-md">
            <p class="text-red-400 font-medium flex items-center gap-2">
                <span>⚠️</span> {{ session('error') }}
            </p>
        </div>
    @endif

    <!-- Builder Form -->
    <div class="rounded-3xl p-6 md:p-10 shadow-2xl mb-12 relative z-10" style="background: var(--bg-card-solid); border: 1px solid var(--border-primary); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <form id="projectorForm" class="space-y-12">
            <!-- Competition Selection -->
            <div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center border border-blue-500/50">
                        <span class="text-blue-400 font-bold">1</span>
                    </div>
                    <h2 class="text-2xl font-bold" style="color: var(--text-primary);">
                        Odaberite lige ili turnire
                    </h2>
                </div>
                    
                    @if($competitions->isEmpty())
                        <div class="text-center py-8">
                            <p style="color: var(--text-tertiary);">Nema dostupnih javnih liga ili turnira.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($competitions as $organizationName => $orgCompetitions)
                                <div class="rounded-lg p-4" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                                    <h3 class="text-xl font-semibold mb-3 flex items-center" style="color: var(--text-primary);">
                                        <span class="mr-2">�</span> {{ $organizationName }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($orgCompetitions as $competition)
                                            <label class="flex items-center p-3 rounded-lg cursor-pointer transition-all hover:bg-white/5" style="border: 1px solid var(--border-primary);">
                                                <input 
                                                    type="checkbox" 
                                                    name="competitions[]" 
                                                    value="{{ $competition->id }}"
                                                    data-name="{{ $competition->name }}"
                                                    data-type="{{ $competition->type }}"
                                                    class="competition-checkbox w-5 h-5 rounded border-gray-600 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-800"
                                                    onchange="updateSelectedList()"
                                                >
                                                <div class="ml-3 flex-1">
                                                    <p class="font-medium" style="color: var(--text-primary);">{{ $competition->name }}</p>
                                                    <p class="text-sm" style="color: var(--text-tertiary);">{{ $competition->sport->name }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Selected Competitions with Duration -->
                <div id="selectedSection" class="mb-8 hidden">
                    <h2 class="text-2xl font-bold mb-4" style="color: var(--text-primary);">
                        2. Podesite trajanje prikaza
                    </h2>

                    <div class="mb-4 flex items-center gap-3">
                        <input
                            type="number"
                            id="uniform_duration"
                            placeholder="Trajanje za sve (sec)"
                            min="5"
                            max="300"
                            class="px-4 py-2 rounded-lg border w-40"
                            style="background: var(--bg-primary); color: var(--text-primary); border-color: var(--border-primary);"
                        >
                        <button type="button" onclick="setUniformForAll()" class="px-4 py-2 rounded-lg font-semibold" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">Postavi za sve</button>
                        <span class="text-sm text-gray-400">Unesite broj sekundi pa kliknite "Postavi za sve" da namjestite sve selektovane turnire.</span>
                    </div>

                    <div class="rounded-lg p-4" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                        <div id="selectedList" class="space-y-3">
                            <!-- Dinamički generisano JavaScript-om -->
                        </div>
                    </div>
                </div>

                <!-- Display Options -->
                <div>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center border border-blue-500/50">
                            <span class="text-blue-400 font-bold">3</span>
                        </div>
                        <h2 class="text-2xl font-bold" style="color: var(--text-primary);">
                            Podesite opcije prikaza
                        </h2>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Layout -->
                        <div class="rounded-2xl p-6" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                            <label class="block text-sm font-bold mb-4 uppercase tracking-wider" style="color: var(--text-tertiary);">
                                📺 Layout prikaza
                            </label>
                            <div class="flex gap-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="layout" value="single" class="peer sr-only" checked>
                                    <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-500/10" style="border-color: var(--border-primary);">
                                        <div class="text-center">
                                            <div class="text-3xl mb-2">📺</div>
                                            <div class="font-bold" style="color: var(--text-primary);">Jedna kolona</div>
                                            <div class="text-xs mt-1" style="color: var(--text-tertiary);">Puni prikaz</div>
                                        </div>
                                    </div>
                                </label>
                                <!-- Split layout option removed -->
                            </div>
                        </div>

                        <!-- Resolution -->
                        <div class="rounded-2xl p-6" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                            <label class="block text-sm font-bold mb-4 uppercase tracking-wider" style="color: var(--text-tertiary);">
                                📱 Rezolucija ekrana
                            </label>
                            <div class="flex gap-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="resolution" value="full" class="peer sr-only" checked>
                                    <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-500/10" style="border-color: var(--border-primary);">
                                        <div class="text-center">
                                            <div class="text-3xl mb-2">📱</div>
                                            <div class="font-bold" style="color: var(--text-primary);">Responzivno</div>
                                            <div class="text-xs mt-1" style="color: var(--text-tertiary);">Full Screen</div>
                                        </div>
                                    </div>
                                </label>
                                <!-- Fixed projector resolution option removed -->
                            </div>
                        </div>

                        <!-- Settings Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Default Duration -->
                            <!-- Default rotation control removed -->

                        

                            <!-- Transition Speed -->
                            <div class="rounded-2xl p-5 group transition-all hover:bg-white/5" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                                <label for="transition" class="block text-sm font-bold mb-3 uppercase tracking-wider" style="color: var(--text-tertiary);">
                                    ⚡ Tranzicija (ms)
                                </label>
                                <input 
                                    type="number" 
                                    name="transition" 
                                    id="transition" 
                                    value="1000" 
                                    min="100" 
                                    max="2000"
                                    step="100"
                                    class="w-full px-4 py-3 rounded-xl border-2 transition-all focus:ring-4 focus:ring-blue-500/20 outline-none" 
                                    style="background: var(--bg-primary); color: var(--text-primary); border-color: var(--border-primary);"
                                >
                            </div>

                            <!-- Live Priority -->
                            <div class="rounded-2xl p-5 flex items-center transition-all hover:bg-white/5" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                                <label class="flex items-center cursor-pointer group w-full">
                                    <div class="relative">
                                        <input 
                                            type="checkbox" 
                                            name="live_priority" 
                                            id="live_priority" 
                                            value="1"
                                            class="peer sr-only"
                                        >
                                        <div class="w-12 h-6 bg-gray-700 rounded-full border border-gray-600 peer-checked:bg-blue-600 transition-all"></div>
                                        <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all peer-checked:left-7"></div>
                                    </div>
                                    <div class="ml-4">
                                        <span class="font-bold block text-sm" style="color: var(--text-primary);">🔴 Live prioritet</span>
                                        <p class="text-xs" style="color: var(--text-tertiary);">Prioritet uživo</p>
                                    </div>
                                </label>
                            </div>
                            <!-- Transition Type -->
                            <div class="rounded-2xl p-5 group transition-all hover:bg-white/5" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                                <label for="transition_type" class="block text-sm font-bold mb-3 uppercase tracking-wider" style="color: var(--text-tertiary);">
                                    🔁 Tranzicija
                                </label>
                                <select name="transition_type" id="transition_type" class="w-full px-4 py-3 rounded-xl border-2 outline-none" style="background: var(--bg-primary); color: var(--text-primary); border-color: var(--border-primary);">
                                    <option value="fade">Fade (blago)</option>
                                    <option value="slide-left">Slide Lijevo</option>
                                    <option value="slide-up">Slide Gore</option>
                                    <option value="zoom" selected>Zoom</option>
                                    <option value="none">Bez tranzicije</option>
                                </select>
                            </div>
                        </div>
                        <!-- QR Codes Builder -->
                        <div class="rounded-2xl p-6" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                            <label class="block text-sm font-bold mb-3 uppercase tracking-wider" style="color: var(--text-tertiary);">🔗 QR kod</label>
                            <div class="flex gap-2 mb-3">
                                <input type="text" id="qr_url_input" placeholder="https://example.com" class="flex-1 px-3 py-2 rounded-lg" style="background: var(--bg-primary); color: var(--text-primary); border: 1px solid var(--border-primary);">
                                <input type="text" id="qr_caption_input" placeholder="Tekst iznad (opcionalno)" class="flex-1 px-3 py-2 rounded-lg" style="background: var(--bg-primary); color: var(--text-primary); border: 1px solid var(--border-primary);">
                                <input type="number" id="qr_count_input" value="1" min="1" max="6" class="w-28 px-3 py-2 rounded-lg" style="background: var(--bg-primary); color: var(--text-primary); border: 1px solid var(--border-primary);">
                                <button type="button" onclick="addQrItem()" class="px-4 py-2 rounded-lg font-semibold" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">Dodaj</button>
                            </div>
                            <div id="qrItemsList" class="space-y-2 text-sm text-gray-300"></div>
                        </div>
                    </div>
                </div>
                    </div>
                </div>

                <!-- Generated URL -->
                <div id="urlSection" class="mb-8 hidden">
                    <h2 class="text-2xl font-bold mb-4" style="color: var(--text-primary);">
                        4. Generisani URL
                    </h2>
                    <div class="rounded-lg p-4" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                        <div class="flex items-center gap-3">
                            <input 
                                type="text" 
                                id="generatedUrl" 
                                readonly
                                class="flex-1 px-4 py-3 rounded-lg border font-mono text-sm" 
                                style="background: var(--bg-primary); color: var(--text-primary); border-color: var(--border-primary);"
                            >
                            <button 
                                type="button" 
                                onclick="copyUrl()" 
                                class="px-6 py-3 rounded-lg font-semibold transition-all hover:scale-105"
                                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;"
                            >
                                📋 Kopiraj
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 justify-center">
                    <button 
                        type="button" 
                        onclick="generateUrl()" 
                        class="px-8 py-4 rounded-lg font-bold text-lg transition-all hover:scale-105 shadow-lg"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;"
                    >
                        🚀 Generiši projektor URL
                    </button>
                    <button 
                        type="button" 
                        onclick="openProjector()" 
                        id="openBtn"
                        class="px-8 py-4 rounded-lg font-bold text-lg transition-all hover:scale-105 shadow-lg hidden"
                        style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;"
                    >
                        👁️ Otvori projektor
                    </button>
                </div>
            </form>
        </div>

        <!-- Instructions -->
        <div class="rounded-3xl p-8 shadow-xl border border-white/5" style="background: rgba(255, 255, 255, 0.02); backdrop-filter: var(--backdrop-blur);">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-2" style="color: var(--text-primary);">
                <span>📖</span> Kako koristiti Builder?
            </h3>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-4" style="color: var(--text-secondary);">
                <li class="flex items-start gap-3">
                    <span class="text-blue-400 font-bold">1.</span>
                    <span>Odaberite jednu ili više liga/turnira koje želite prikazati.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-blue-400 font-bold">2.</span>
                    <span>Kliknite "Generiši" i otvorite link na projektoru ili TV-u.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    let selectedCompetitions = [];
    let orderedSelections = [];

    function updateSelectedList() {
        // Determine currently checked ids in DOM order
        const checkboxes = Array.from(document.querySelectorAll('.competition-checkbox'));
        const checkedIds = checkboxes.filter(cb => cb.checked).map(cb => cb.value);

        // Reconcile orderedSelections: keep existing order for retained ids, append new ones
        // Keep QR items and checked competitions
        orderedSelections = orderedSelections.filter(id => {
            if (id && id.toString().startsWith('qr_')) return true;
            return checkedIds.includes(id);
        });

        // Add only those from checkedIds that aren't already in orderedSelections
        checkedIds.forEach(id => { 
            if (!orderedSelections.includes(id)) orderedSelections.push(id); 
        });

        // Build selectedCompetitions from orderedSelections
        selectedCompetitions = orderedSelections.map((id, index) => {
            if (id && id.toString().startsWith('qr_')) {
                const it = window.qrItems.find(q => q.id === id);
                return {
                    id: id,
                    name: it ? (it.text || 'QR Kod') : 'QR Kod',
                    type: 'qr',
                    order: index
                };
            }
            const cb = document.querySelector(`.competition-checkbox[value="${id}"]`);
            return {
                id: id,
                name: cb ? cb.dataset.name : '',
                type: cb ? cb.dataset.type : '',
                order: index
            };
        });

        renderSelectedList();
    }

    function removeSelection(index) {
        const item = selectedCompetitions[index];
        if (item.type === 'qr') {
            orderedSelections.splice(index, 1);
            updateSelectedList();
        } else {
            // Uncheck the checkbox
            const cb = document.querySelector(`.competition-checkbox[value="${item.id}"]`);
            if (cb) {
                cb.checked = false;
                updateSelectedList();
            }
        }
    }

    function renderSelectedList() {
        const selectedSection = document.getElementById('selectedSection');
        const selectedList = document.getElementById('selectedList');

        if (selectedCompetitions.length > 0) {
            selectedSection.classList.remove('hidden');
            selectedList.innerHTML = selectedCompetitions.map((comp, index) => `
                <div class="flex items-center gap-4 p-3 rounded-lg group" style="background: var(--bg-primary); border: 1px solid var(--border-primary);">
                    <span class="text-2xl font-bold text-blue-400">${index + 1}</span>
                    <div class="flex-1">
                        <p class="font-medium" style="color: var(--text-primary);">${comp.name}</p>
                    </div>
                    ${comp.type === 'qr' ? `
                    <div class="px-2 py-1 rounded bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase tracking-widest border border-blue-500/20">QR SLIDE</div>
                    ` : ''}
                    ${comp.type === 'tournament' ? `
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-bold uppercase" style="color: var(--text-tertiary);">Faza:</label>
                        <div class="flex gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="phase_${comp.id}" value="auto" class="peer sr-only" checked>
                                <div class="px-3 py-1 rounded-lg border-2 text-xs font-bold transition-all peer-checked:border-blue-500 peer-checked:bg-blue-500/20" style="border-color: var(--border-primary); color: var(--text-primary);">
                                    🤖 Auto
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="phase_${comp.id}" value="groups" class="peer sr-only">
                                <div class="px-3 py-1 rounded-lg border-2 text-xs font-bold transition-all peer-checked:border-blue-500 peer-checked:bg-blue-500/20" style="border-color: var(--border-primary); color: var(--text-primary);">
                                    📊 Grupe
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="phase_${comp.id}" value="knockout" class="peer sr-only">
                                <div class="px-3 py-1 rounded-lg border-2 text-xs font-bold transition-all peer-checked:border-blue-500 peer-checked:bg-blue-500/20" style="border-color: var(--border-primary); color: var(--text-primary);">
                                    🏆 Knockout
                                </div>
                            </label>
                        </div>
                    </div>
                    ` : ''}
                    <div class="flex items-center gap-2">
                        <input 
                            type="number" 
                            id="duration_${comp.id}" 
                            value="20" 
                            min="5" 
                            max="300"
                            placeholder="20"
                            class="w-20 px-3 py-2 rounded-xl border-2 text-center font-bold" 
                            style="background: var(--bg-secondary); color: var(--text-primary); border-color: var(--border-primary);"
                        >
                        <span class="text-sm font-medium uppercase tracking-tighter" style="color: var(--text-tertiary);">sec</span>
                    </div>
                    <div class="flex gap-1">
                        <button type="button" onclick="moveUp(${index})" class="p-2 rounded hover:bg-white/10" title="Pomjeri gore" ${index === 0 ? 'disabled style="opacity: 0.3;"' : ''}>
                            <span style="color: var(--text-primary);">⬆️</span>
                        </button>
                        <button type="button" onclick="moveDown(${index})" class="p-2 rounded hover:bg-white/10" title="Pomjeri dole" ${index === selectedCompetitions.length - 1 ? 'disabled style="opacity: 0.3;"' : ''}>
                            <span style="color: var(--text-primary);">⬇️</span>
                        </button>
                        <button type="button" onclick="removeSelection(${index})" class="p-2 rounded hover:bg-red-500/20 text-red-400 opacity-0 group-hover:opacity-100 transition-opacity" title="Ukloni">
                            🗑️
                        </button>
                    </div>
                </div>
            `).join('');
            // Apply uniform duration if set
            applyUniformDuration();
        } else {
            selectedSection.classList.add('hidden');
            selectedList.innerHTML = '';
        }
    }

    function applyUniformDuration() {
        const uniform = document.getElementById('uniform_duration');
        if (!uniform) return;
        const val = uniform.value;
        if (!val) return;

        selectedCompetitions.forEach(c => {
            const el = document.getElementById(`duration_${c.id}`);
            if (el) el.value = val;
        });
    }

    function setUniformForAll() {
        const uniform = document.getElementById('uniform_duration');
        if (!uniform) return;
        const val = uniform.value;
        if (!val) {
            alert('Molimo unesite trajanje u sekundama (5-300).');
            return;
        }
        // Apply to inputs and ensure UI updates
        selectedCompetitions.forEach(c => {
            const el = document.getElementById(`duration_${c.id}`);
            if (el) el.value = val;
        });
        // Optional: scroll to selected section to show changes
        const selectedSection = document.getElementById('selectedSection');
        if (selectedSection) selectedSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function moveUp(index) {
        if (index === 0) return;
        // Swap in orderedSelections and re-render
        const tmp = orderedSelections[index - 1];
        orderedSelections[index - 1] = orderedSelections[index];
        orderedSelections[index] = tmp;
        updateSelectedList();
    }

    function moveDown(index) {
        if (index === orderedSelections.length - 1) return;
        const tmp = orderedSelections[index + 1];
        orderedSelections[index + 1] = orderedSelections[index];
        orderedSelections[index] = tmp;
        updateSelectedList();
    }

    function generateUrl() {
        if (selectedCompetitions.length === 0) {
            alert('Molimo odaberite najmanje jednu ligu/turnir!');
            return;
        }

        // Build sequence tokens from orderedSelections (mix of competitions and qr items)
        const sequenceTokens = [];
        const competitionIdsInOrder = [];
        const competitionDurations = [];
        const competitionPhases = [];

        const uniformElem = document.getElementById('uniform_duration');
        const uniformDuration = uniformElem && uniformElem.value ? uniformElem.value : null;
        const defaultDurationElem = document.getElementById('default_duration');
        const defaultDuration = defaultDurationElem ? defaultDurationElem.value : '20';

        orderedSelections.forEach(token => {
            if (token && token.toString().startsWith('qr_')) {
                // find index of qr in window.qrItems
                const idx = window.qrItems.findIndex(q => q.id === token);
                if (idx !== -1) {
                    const count = window.qrItems[idx].count || 1;
                    for (let k = 0; k < count; k++) sequenceTokens.push(`q:${idx}`);
                }
            } else {
                // competition id
                sequenceTokens.push(`c:${token}`);
                competitionIdsInOrder.push(token);
                // duration for this competition
                const input = document.getElementById(`duration_${token}`);
                const dur = uniformDuration ? uniformDuration : (input ? input.value : defaultDuration);
                competitionDurations.push(dur);
                // phase for this competition
                const phaseRadio = document.querySelector(`input[name="phase_${token}"]:checked`);
                competitionPhases.push(phaseRadio ? phaseRadio.value : 'auto');
            }
        });

        const mode = 'both';
        const layoutRadio = document.querySelector('input[name="layout"]:checked');
        const layout = layoutRadio ? layoutRadio.value : 'single';
        
        const resolutionRadio = document.querySelector('input[name="resolution"]:checked');
        const resolution = resolutionRadio ? resolutionRadio.value : 'full';

        const transitionTypeElem = document.querySelector('select[name="transition_type"]');
        const transitionType = transitionTypeElem ? transitionTypeElem.value : 'fade';
        const transition = document.getElementById('transition').value;
        const livePriority = document.getElementById('live_priority').checked ? '1' : '0';

        const baseUrl = window.location.origin;
        // Collect per-QR durations from any duration inputs in the selected list
        if (window.qrItems && window.qrItems.length) {
            window.qrItems.forEach(it => {
                const el = document.getElementById(`duration_${it.id}`);
                if (el) it.duration = el.value;
            });
        }
        
        // Helper to encode UTF-8 to base64 safely
        const safeBtoa = (str) => {
            try {
                return btoa(unescape(encodeURIComponent(str)));
            } catch(e) {
                return btoa(str);
            }
        };

        const qrs = window.qrItems && window.qrItems.length ? encodeURIComponent(safeBtoa(JSON.stringify(window.qrItems))) : '';
        const seq = sequenceTokens.length ? encodeURIComponent(sequenceTokens.join(',')) : '';
        const idsParam = competitionIdsInOrder.join(',');
        const durationsParam = competitionDurations.join(',');
        const phasesParam = competitionPhases.join(',');

        const url = `${baseUrl}/projector/display?ids=${idsParam}&durations=${durationsParam}&phases=${phasesParam}&sequence=${seq}&mode=${mode}&layout=${layout}&resolution=${resolution}&default_duration=${defaultDuration}&transition=${transition}&transition_type=${transitionType}&live_priority=${livePriority}${qrs ? `&qrs=${qrs}` : ''}`;

        document.getElementById('generatedUrl').value = url;
        document.getElementById('urlSection').classList.remove('hidden');
        document.getElementById('openBtn').classList.remove('hidden');

        // Scroll to URL
        document.getElementById('urlSection').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function copyUrl() {
        const urlInput = document.getElementById('generatedUrl');
        urlInput.select();
        document.execCommand('copy');
        
        // Visual feedback
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '✅ Kopirano!';
        button.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)';
        }, 2000);
    }

    function openProjector() {
        const url = document.getElementById('generatedUrl').value;
        if (url) {
            window.open(url, '_blank');
        }
    }

    // Apply uniform duration when user changes the uniform input
    const uniformInput = document.getElementById('uniform_duration');
    if (uniformInput) {
        uniformInput.addEventListener('input', () => {
            applyUniformDuration();
        });
    }

    // QR items management
    window.qrItems = window.qrItems || [];

    function renderQrItems() {
        const list = document.getElementById('qrItemsList');
        if (!list) return;
        if (window.qrItems.length === 0) {
            list.innerHTML = '<p class="text-xs italic text-gray-500 text-center">Nema kreiranih QR kodova</p>';
            return;
        }
        list.innerHTML = window.qrItems.map((it, idx) => `
            <div class="flex items-center justify-between p-3 rounded-xl border border-white/5 bg-white/5 group">
                <div class="flex-1 min-w-0 pr-4">
                    <p class="font-bold text-sm truncate" style="color: var(--text-primary);">${it.text || 'Bez naslova'}</p>
                    <p class="text-[10px] text-gray-500 truncate">${it.url}</p>
                </div>
                <div class="flex items-center gap-2 text-right">
                    <button type="button" onclick="insertQrToOrdered(${idx})" class="px-4 py-2 rounded-lg bg-green-500/20 text-green-400 text-xs font-bold hover:bg-green-500/30 transition-colors">
                        Umetni
                    </button>
                    <button type="button" onclick="removeQrItem(${idx})" class="p-2 rounded-lg bg-red-500/10 text-red-500/50 hover:text-red-500 hover:bg-red-500/20 transition-all">
                        🗑️
                    </button>
                </div>
            </div>
        `).join('');
    }

    function addQrItem() {
        const urlEl = document.getElementById('qr_url_input');
        const captionEl = document.getElementById('qr_caption_input');
        const countEl = document.getElementById('qr_count_input');
        if (!urlEl || !countEl) return;
        const url = urlEl.value.trim();
        const text = captionEl ? captionEl.value.trim() : '';
        const count = parseInt(countEl.value, 10) || 1;
        if (!url) { alert('Unesite URL za QR kod'); return; }
        const id = 'qr_' + Math.random().toString(36).substr(2, 9);
        window.qrItems.push({ id: id, url: url, count: Math.max(1, Math.min(6, count)), text: text });
        urlEl.value = '';
        captionEl.value = '';
        countEl.value = '1';
        renderQrItems();
    }

    function removeQrItem(idx) {
        window.qrItems.splice(idx, 1);
        renderQrItems();
    }

    function insertQrToOrdered(idx) {
        const item = window.qrItems[idx];
        if (!item) return;
        // insert at end of ordered selections
        orderedSelections.push(item.id);
        updateSelectedList();
    }

    // initialize list if any
    renderQrItems();
</script>
@endsection
