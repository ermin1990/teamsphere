<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 border border-transparent rounded-xl font-semibold text-xs uppercase tracking-widest hover:brightness-110 focus:outline-none transition-all active:scale-95']) }}
    style="background: #dc2626; color: #fff;">
    {{ $slot }}
</button>
