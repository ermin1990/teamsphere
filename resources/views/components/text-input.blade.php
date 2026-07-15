@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl shadow-sm focus:outline-none focus:border-[var(--accent-blue)] focus:ring-1 focus:ring-[var(--accent-blue)] transition-all']) }}
    style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
