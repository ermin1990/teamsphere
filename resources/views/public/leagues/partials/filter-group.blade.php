@php
    /** Vertical filter link list (used for both city and sport filters on the
     *  public leagues browse page) - preserves the other active filter param
     *  when toggling, full page reload (consistent with other filter forms
     *  in the app), so no JS is required. */
    $activeId = (int) request($param);
@endphp
<h3 class="text-xs font-bold uppercase tracking-wider mb-2.5" style="color: var(--text-tertiary);">{{ $label }}</h3>
<div class="flex flex-wrap lg:flex-col gap-1.5">
    <a href="{{ route('competitions.index', array_filter([$param === 'city_id' ? null : 'city_id' => request('city_id'), $param === 'sport_id' ? null : 'sport_id' => request('sport_id')])) }}"
       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all lg:text-left"
       style="{{ !$activeId
            ? 'background: var(--accent-blue); color: #14141F;'
            : 'background: var(--bg-hover); color: var(--text-secondary);' }}">
        {{ $allLabel }}
    </a>
    @foreach($items as $item)
        <a href="{{ route('competitions.index', array_filter([
                'city_id' => $param === 'city_id' ? $item->id : request('city_id'),
                'sport_id' => $param === 'sport_id' ? $item->id : request('sport_id'),
            ])) }}"
           class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all lg:text-left truncate"
           style="{{ $activeId === $item->id
                ? 'background: var(--accent-blue); color: #14141F;'
                : 'background: var(--bg-hover); color: var(--text-secondary);' }}">
            {{ $item->name }}
        </a>
    @endforeach
</div>
