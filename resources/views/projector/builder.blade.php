@extends('layouts.public')

@section('title', 'Projektor Builder - TeamSphere')

@section('content')
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-2" style="color: var(--text-primary);">
                🎬 Projektor Builder
            </h1>
            <p class="text-lg" style="color: var(--text-secondary);">
                Kreirajte personalizovani projektor za prikaz liga i turnira
            </p>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-500/20 border border-red-500/50">
                <p class="text-red-400">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Builder Form -->
        <div class="rounded-2xl p-6 shadow-2xl mb-8" style="background: var(--bg-card); backdrop-filter: var(--backdrop-blur); border: 1px solid var(--border-primary);">
            <form id="projectorForm">
                <!-- Competition Selection -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4" style="color: var(--text-primary);">
                        1. Odaberite lige/turnire
                    </h2>
                    
                    @if($competitions->isEmpty())
                        <div class="text-center py-8">
                            <p style="color: var(--text-tertiary);">Nema dostupnih javnih liga ili turnira.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($competitions as $sportName => $sportCompetitions)
                                <div class="rounded-lg p-4" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                                    <h3 class="text-xl font-semibold mb-3 flex items-center" style="color: var(--text-primary);">
                                        <span class="mr-2">🏆</span> {{ $sportName }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($sportCompetitions as $competition)
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
                                                    <p class="text-sm" style="color: var(--text-tertiary);">{{ $competition->organization->name }}</p>
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
                    <div class="rounded-lg p-4" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                        <div id="selectedList" class="space-y-3">
                            <!-- Dinamički generisano JavaScript-om -->
                        </div>
                    </div>
                </div>

                <!-- Display Options -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4" style="color: var(--text-primary);">
                        3. Opcije prikaza
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Layout -->
                        <div class="rounded-lg p-4" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                                Layout
                            </label>
                            <select name="layout" id="layout" class="w-full px-3 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500" style="background: var(--bg-primary); color: var(--text-primary); border-color: var(--border-primary);">
                                <option value="single">Jedna liga (puno)</option>
                                <option value="split">Split (tabela + mečevi)</option>
                            </select>
                        </div>

                        <!-- Default Duration -->
                        <div class="rounded-lg p-4" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                                Podrazumijevano trajanje (s)
                            </label>
                            <input 
                                type="number" 
                                name="default_duration" 
                                id="default_duration" 
                                value="20" 
                                min="5" 
                                max="300"
                                class="w-full px-3 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500" 
                                style="background: var(--bg-primary); color: var(--text-primary); border-color: var(--border-primary);"
                            >
                        </div>

                        <!-- Transition Speed -->
                        <div class="rounded-lg p-4" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                                Brzina tranzicije (ms)
                            </label>
                            <input 
                                type="number" 
                                name="transition" 
                                id="transition" 
                                value="500" 
                                min="100" 
                                max="2000"
                                step="100"
                                class="w-full px-3 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500" 
                                style="background: var(--bg-primary); color: var(--text-primary); border-color: var(--border-primary);"
                            >
                        </div>

                        <!-- Live Priority -->
                        <div class="rounded-lg p-4 flex items-center" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="live_priority" 
                                    id="live_priority" 
                                    value="1"
                                    class="w-5 h-5 rounded border-gray-600 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-800"
                                >
                                <div class="ml-3">
                                    <span class="font-medium" style="color: var(--text-primary);">Live prioritet</span>
                                    <p class="text-xs" style="color: var(--text-tertiary);">Produži vrijeme za uživo mečeve</p>
                                </div>
                            </label>
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
        <div class="rounded-2xl p-6 shadow-xl" style="background: var(--bg-card); backdrop-filter: var(--backdrop-blur); border: 1px solid var(--border-primary);">
            <h3 class="text-xl font-bold mb-4" style="color: var(--text-primary);">📖 Uputstvo</h3>
            <ul class="space-y-2" style="color: var(--text-secondary);">
                <li>✅ Odaberite jednu ili više liga/turnira koje želite prikazati</li>
                <li>⏱️ Podesite trajanje prikaza za svaku ligu (u sekundama)</li>
                <li>🎨 Izaberite opcije prikaza (samo tabele, samo mečevi, ili oboje)</li>
                <li>🔗 Kliknite "Generiši projektor URL" za kreiranje linka</li>
                <li>📺 Kopirajte URL i otvorite ga na TV-u ili projektoru</li>
                <li>🔄 Projektor će automatski rotirati kroz odabrane lige</li>
                <li>⚡ Live prioritet automatski produžava vrijeme za lige sa uživo mečevima</li>
            </ul>
        </div>
    </div>
</div>

<script>
    let selectedCompetitions = [];

    function updateSelectedList() {
        const checkboxes = document.querySelectorAll('.competition-checkbox:checked');
        selectedCompetitions = Array.from(checkboxes).map((cb, index) => ({
            id: cb.value,
            name: cb.dataset.name,
            type: cb.dataset.type,
            order: index
        }));

        const selectedSection = document.getElementById('selectedSection');
        const selectedList = document.getElementById('selectedList');

        if (selectedCompetitions.length > 0) {
            selectedSection.classList.remove('hidden');
            selectedList.innerHTML = selectedCompetitions.map((comp, index) => `
                <div class="flex items-center gap-4 p-3 rounded-lg" style="background: var(--bg-primary); border: 1px solid var(--border-primary);">
                    <span class="text-2xl font-bold text-blue-400">${index + 1}</span>
                    <div class="flex-1">
                        <p class="font-medium" style="color: var(--text-primary);">${comp.name}</p>
                    </div>
                    ${comp.type === 'tournament' ? `
                    <div class="flex items-center gap-2">
                        <select 
                            id="phase_${comp.id}" 
                            class="px-3 py-2 rounded-lg border text-sm" 
                            style="background: var(--bg-secondary); color: var(--text-primary); border-color: var(--border-primary);"
                        >
                            <option value="auto">Auto</option>
                            <option value="groups">Grupna faza</option>
                            <option value="knockout">Knockout faza</option>
                        </select>
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
                            class="w-20 px-3 py-2 rounded-lg border text-center" 
                            style="background: var(--bg-secondary); color: var(--text-primary); border-color: var(--border-primary);"
                        >
                        <span style="color: var(--text-tertiary);">sekundi</span>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="moveUp(${index})" class="p-2 rounded hover:bg-white/10" ${index === 0 ? 'disabled style="opacity: 0.5;"' : ''}>
                            <span style="color: var(--text-primary);">⬆️</span>
                        </button>
                        <button type="button" onclick="moveDown(${index})" class="p-2 rounded hover:bg-white/10" ${index === selectedCompetitions.length - 1 ? 'disabled style="opacity: 0.5;"' : ''}>
                            <span style="color: var(--text-primary);">⬇️</span>
                        </button>
                    </div>
                </div>
            `).join('');
        } else {
            selectedSection.classList.add('hidden');
        }
    }

    function moveUp(index) {
        if (index === 0) return;
        
        const checkboxes = Array.from(document.querySelectorAll('.competition-checkbox:checked'));
        const current = checkboxes[index];
        const previous = checkboxes[index - 1];
        
        // Swap order visually by unchecking and rechecking
        current.checked = false;
        previous.checked = false;
        
        setTimeout(() => {
            previous.checked = true;
            current.checked = true;
            updateSelectedList();
        }, 10);
    }

    function moveDown(index) {
        if (index === selectedCompetitions.length - 1) return;
        moveUp(index + 1);
    }

    function generateUrl() {
        if (selectedCompetitions.length === 0) {
            alert('Molimo odaberite najmanje jednu ligu/turnir!');
            return;
        }

        const ids = selectedCompetitions.map(c => c.id).join(',');
        const durations = selectedCompetitions.map(c => {
            const input = document.getElementById(`duration_${c.id}`);
            return input ? input.value : document.getElementById('default_duration').value;
        }).join(',');
        
        const phases = selectedCompetitions.map(c => {
            const phaseSelect = document.getElementById(`phase_${c.id}`);
            return phaseSelect ? phaseSelect.value : 'auto';
        }).join(',');

        const mode = 'both';
        const layout = document.getElementById('layout').value;
        const defaultDuration = document.getElementById('default_duration').value;
        const transition = document.getElementById('transition').value;
        const livePriority = document.getElementById('live_priority').checked ? '1' : '0';

        const baseUrl = window.location.origin;
        const url = `${baseUrl}/projector/display?ids=${ids}&durations=${durations}&phases=${phases}&mode=${mode}&layout=${layout}&default_duration=${defaultDuration}&transition=${transition}&live_priority=${livePriority}`;

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
</script>
@endsection
