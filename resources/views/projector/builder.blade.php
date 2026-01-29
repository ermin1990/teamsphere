@extends('layouts.public')

@section('title', 'Projektor Builder - TeamSphere')

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
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="layout" value="split" class="peer sr-only">
                                    <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-500/10" style="border-color: var(--border-primary);">
                                        <div class="text-center">
                                            <div class="text-3xl mb-2">🌓</div>
                                            <div class="font-bold" style="color: var(--text-primary);">Split Layout</div>
                                            <div class="text-xs mt-1" style="color: var(--text-tertiary);">Tabela + Mečevi</div>
                                        </div>
                                    </div>
                                </label>
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
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="resolution" value="1024x768" class="peer sr-only">
                                    <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-500/10" style="border-color: var(--border-primary);">
                                        <div class="text-center">
                                            <div class="text-3xl mb-2">📽️</div>
                                            <div class="font-bold" style="color: var(--text-primary);">Projektor</div>
                                            <div class="text-xs mt-1" style="color: var(--text-tertiary);">1024x768</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Settings Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Default Duration -->
                            <div class="rounded-2xl p-5 group transition-all hover:bg-white/5" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                                <label for="default_duration" class="block text-sm font-bold mb-3 uppercase tracking-wider" style="color: var(--text-tertiary);">
                                    ⏱️ Rotacija (sec)
                                </label>
                                <input 
                                    type="number" 
                                    name="default_duration" 
                                    id="default_duration" 
                                    value="20" 
                                    min="5" 
                                    max="300"
                                    class="w-full px-4 py-3 rounded-xl border-2 transition-all focus:ring-4 focus:ring-blue-500/20 outline-none" 
                                    style="background: var(--bg-primary); color: var(--text-primary); border-color: var(--border-primary);"
                                >
                            </div>

                            <!-- Transition Speed -->
                            <div class="rounded-2xl p-5 group transition-all hover:bg-white/5" style="background: var(--bg-secondary); border: 1px solid var(--border-secondary);">
                                <label for="transition" class="block text-sm font-bold mb-3 uppercase tracking-wider" style="color: var(--text-tertiary);">
                                    ⚡ Tranzicija (ms)
                                </label>
                                <input 
                                    type="number" 
                                    name="transition" 
                                    id="transition" 
                                    value="500" 
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
                    <span>Odaberite jednu ili više liga/turnira koje želite rotirati na ekranu.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-blue-400 font-bold">2.</span>
                    <span>Postavite trajanje (u sekundama) koliko će se svaka liga zadržati prije promjene.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-blue-400 font-bold">3.</span>
                    <span>Izaberite Split layout ako želite tabele i mečeve istovremeno.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-blue-400 font-bold">4.</span>
                    <span>Kliknite "Generiši" i otvorite link na projektoru ili TV-u.</span>
                </li>
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
            const phaseRadio = document.querySelector(`input[name="phase_${c.id}"]:checked`);
            return phaseRadio ? phaseRadio.value : 'auto';
        }).join(',');

        const mode = 'both';
        const layoutRadio = document.querySelector('input[name="layout"]:checked');
        const layout = layoutRadio ? layoutRadio.value : 'single';
        
        const resolutionRadio = document.querySelector('input[name="resolution"]:checked');
        const resolution = resolutionRadio ? resolutionRadio.value : 'full';
        
        const defaultDuration = document.getElementById('default_duration').value;
        const transition = document.getElementById('transition').value;
        const livePriority = document.getElementById('live_priority').checked ? '1' : '0';

        const baseUrl = window.location.origin;
        const url = `${baseUrl}/projector/display?ids=${ids}&durations=${durations}&phases=${phases}&mode=${mode}&layout=${layout}&resolution=${resolution}&default_duration=${defaultDuration}&transition=${transition}&live_priority=${livePriority}`;

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
