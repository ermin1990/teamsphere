<div id="floating-success" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 max-w-xl bg-green-600 text-white px-2 py-3 rounded-lg shadow-lg flex items-start gap-3 transition-opacity duration-300 ease-in-out">
    <div class="flex-shrink-0 mt-0.5">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    <div class="flex-1 text-sm whitespace-nowrap">
        {{ session('success') }}
    </div>
    <button id="floating-success-close" class="text-white opacity-80 hover:opacity-100 ml-3">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('floating-success');
        if (!el) return;

        // Auto-hide after 3.5s
        const hideTimeout = setTimeout(() => {
            el.style.opacity = '0';
            // remove after transition
            el.addEventListener('transitionend', () => el.remove(), { once: true });
        }, 3500);

        // Allow manual close
        const btn = document.getElementById('floating-success-close');
        if (btn) {
            btn.addEventListener('click', () => {
                clearTimeout(hideTimeout);
                el.style.opacity = '0';
                el.addEventListener('transitionend', () => el.remove(), { once: true });
            });
        }
    });
</script>
