<!-- PWA Install Prompt -->
<div id="pwa-install-prompt" class="fixed bottom-0 left-0 right-0 z-50 transform translate-y-full transition-transform duration-500 ease-out md:bottom-4 md:left-auto md:right-4 md:max-w-sm" style="display: none;">
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-purple-700 text-white shadow-2xl rounded-t-2xl md:rounded-2xl border-2 border-blue-400/30 overflow-hidden">
        <!-- Body -->
        <div class="px-6 py-5">
            <p class="text-sm text-blue-50 mb-4 leading-relaxed">
                Dodajte TeamSphere na vaš početni ekran za brži pristup i bolje iskustvo korištenja aplikacije.
            </p>

            <!-- Features -->
            <div class="space-y-2 mb-5">
                <div class="flex items-center space-x-2 text-sm">
                    <svg class="w-5 h-5 text-green-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span class="text-blue-50">Instantan pristup</span>
                </div>
                <div class="flex items-center space-x-2 text-sm">
                    <svg class="w-5 h-5 text-green-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-blue-50">Kao native aplikacija</span>
                </div>
                <div class="flex items-center space-x-2 text-sm">
                    <svg class="w-5 h-5 text-green-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-blue-50">Bez zauzimanja memorije</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col space-y-2">
                <button onclick="installPWA()" class="w-full bg-white text-blue-700 font-bold py-3 px-6 rounded-xl hover:bg-blue-50 transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Instaliraj sada</span>
                </button>
                <button onclick="dismissPWAPrompt()" class="w-full text-white/80 font-medium py-2 px-6 hover:text-white transition-colors text-sm">
                    Možda kasnije
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#pwa-install-prompt.show {
    transform: translateY(0);
}

@media (min-width: 768px) {
    #pwa-install-prompt.show {
        transform: translateY(0);
    }
}

/* Smooth entrance animation */
@keyframes slideUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

#pwa-install-prompt.show {
    animation: slideUp 0.5s ease-out forwards;
}
</style>

<script>
let deferredPrompt;
let promptShown = false;

console.log('PWA: Install prompt component loaded');

// Check if PWA is supported
if ('serviceWorker' in navigator && 'BeforeInstallPromptEvent' in window) {
    console.log('PWA: Browser supports PWA');
} else {
    console.log('PWA: Browser does not fully support PWA');
}
window.addEventListener('beforeinstallprompt', (e) => {
    console.log('PWA: beforeinstallprompt event fired');
    e.preventDefault();
    deferredPrompt = e;
    
    // Check if user has dismissed the prompt before
    const dismissed = localStorage.getItem('pwa-prompt-dismissed');
    const dismissedTime = localStorage.getItem('pwa-prompt-dismissed-time');
    
    // Show prompt if not dismissed or if dismissed more than 7 days ago
    if (!dismissed || (dismissedTime && Date.now() - parseInt(dismissedTime) > 7 * 24 * 60 * 60 * 1000)) {
        console.log('PWA: Showing install prompt');
        showPWAPrompt();
    } else {
        console.log('PWA: Prompt dismissed recently, not showing');
    }
});

function showPWAPrompt() {
    if (promptShown) return;
    
    const prompt = document.getElementById('pwa-install-prompt');
    if (prompt) {
        // Wait a bit before showing to not overwhelm the user
        setTimeout(() => {
            prompt.classList.add('show');
            promptShown = true;
        }, 2000); // Show after 2 seconds
    }
}

function installPWA() {
    const prompt = document.getElementById('pwa-install-prompt');
    
    if (!deferredPrompt) {
        console.log('PWA: No deferred prompt available');
        alert('Instalacija nije dostupna. Možda je aplikacija već instalirana ili vaš browser ne podržava PWA.');
        return;
    }

    // Show the install prompt
    deferredPrompt.prompt();

    // Wait for the user to respond to the prompt
    deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
            console.log('PWA: User accepted the install prompt');
            
            // Hide the custom prompt
            if (prompt) {
                prompt.classList.remove('show');
                setTimeout(() => {
                    prompt.style.display = 'none';
                }, 500);
            }
        } else {
            console.log('PWA: User dismissed the install prompt');
        }
        
        // Clear the deferredPrompt
        deferredPrompt = null;
    });
}

function dismissPWAPrompt() {
    const prompt = document.getElementById('pwa-install-prompt');
    
    if (prompt) {
        prompt.classList.remove('show');
        setTimeout(() => {
            prompt.style.display = 'none';
        }, 500);
    }

    // Remember that user dismissed the prompt
    localStorage.setItem('pwa-prompt-dismissed', 'true');
    localStorage.setItem('pwa-prompt-dismissed-time', Date.now().toString());
}

// Check if already installed
window.addEventListener('appinstalled', () => {
    console.log('PWA: App was installed');
    const prompt = document.getElementById('pwa-install-prompt');
    if (prompt) {
        prompt.style.display = 'none';
    }
    localStorage.setItem('pwa-installed', 'true');
});

// Check if running as installed PWA
if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
    console.log('PWA: Running as installed app');
    const prompt = document.getElementById('pwa-install-prompt');
    if (prompt) {
        prompt.style.display = 'none';
    }
}
</script>
