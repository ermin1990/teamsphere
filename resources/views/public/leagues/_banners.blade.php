{{--
    Renders active banner ads for a given placement (see App\Models\Banner
    for placement constants). Expects $placement. Self-contained query so it
    can be included from any public page without the controller needing to
    pass banner data explicitly.
--}}
@php
    $banners = \App\Models\Banner::active()->forPlacement($placement)->get();
@endphp
@if($banners->isNotEmpty())
    <div class="space-y-4">
        @foreach($banners as $banner)
            <div class="bg-surface-container-low border border-outline-variant rounded-xl overflow-hidden">
                @if($banner->link_url)
                    <a href="{{ $banner->link_url }}" target="_blank" rel="noopener sponsored">
                        <img src="{{ $banner->imageSrc() }}" alt="{{ $banner->title ?? 'Baner' }}" class="w-full h-auto">
                    </a>
                @else
                    <img src="{{ $banner->imageSrc() }}" alt="{{ $banner->title ?? 'Baner' }}" class="w-full h-auto">
                @endif
            </div>
        @endforeach
    </div>
@endif
