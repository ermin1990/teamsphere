<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 border border-transparent rounded-xl font-semibold text-xs uppercase tracking-widest focus:outline-none transition-all active:scale-95']) }}
    style="background: var(--accent-blue); color: #14141F;">
    {{ $slot }}
</button>
