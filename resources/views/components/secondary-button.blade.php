<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-5 py-2.5 rounded-xl font-semibold text-xs uppercase tracking-widest focus:outline-none disabled:opacity-25 transition-all active:scale-95']) }}
    style="background: var(--bg-tertiary); border: 1px solid var(--border-primary); color: var(--text-primary);">
    {{ $slot }}
</button>
