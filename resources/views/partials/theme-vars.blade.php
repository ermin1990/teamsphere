{{--
    Single source of truth for the teal theme's colors (public + player
    pages, landing, auth). Every page's inline tailwind.config references
    these as var(--c-*) instead of hardcoding hex, so light/dark only ever
    needs to change here. Resolved from the authenticated user's saved
    `theme` preference (Profile settings); guests and users who haven't
    picked one get dark, the site's original look.
--}}
@php
    $__theme = optional(auth()->user())->theme === 'light' ? 'light' : 'dark';

    $__themeColors = [
        'dark' => [
            'surface-container-lowest' => '#0b0e14',
            'surface-dim' => '#10131a',
            'surface' => '#10131a',
            'surface-container-low' => '#191c22',
            'surface-container' => '#1d2026',
            'surface-container-high' => '#272a31',
            'surface-container-highest' => '#32353c',
            'surface-variant' => '#32353c',
            'surface-bright' => '#363940',
            'on-surface' => '#e1e2eb',
            'on-surface-variant' => '#bacac5',
            'outline' => '#859490',
            'outline-variant' => '#3c4a46',
            'primary' => '#57f1db',
            'primary-container' => '#2dd4bf',
            'on-primary' => '#003731',
            'on-primary-container' => '#00574d',
            'secondary' => '#ffb95f',
            'secondary-container' => '#ee9800',
            'on-secondary-container' => '#5b3800',
            'tertiary-container' => '#b3bed5',
            'on-tertiary-container' => '#424d61',
            'error' => '#ffb4ab',
            'error-container' => '#93000a',
            'on-error-container' => '#ffdad6',
            'primary-rgb' => '87, 241, 219',
            // "Soft" chip/badge backgrounds (win-rate pill, avatar tile, etc.) -
            // a translucent tint reads fine over the dark canvas, but the same
            // opacity math over a light canvas looks washed-out, so light gets
            // its own tuned solid tone instead of the same formula.
            'primary-soft' => 'rgba(87, 241, 219, 0.18)',
            'error-soft' => 'rgba(255, 180, 171, 0.18)',
            'secondary-soft' => 'rgba(255, 185, 95, 0.18)',
        ],
        'light' => [
            'surface-container-lowest' => '#F7F8FA',
            'surface-dim' => '#EEF0F2',
            'surface' => '#F7F8FA',
            'surface-container-low' => '#FFFFFF',
            'surface-container' => '#FFFFFF',
            'surface-container-high' => '#F1F3F5',
            'surface-container-highest' => '#E7EAED',
            'surface-variant' => '#E7EAED',
            'surface-bright' => '#FFFFFF',
            'on-surface' => '#15181E',
            'on-surface-variant' => '#5B6472',
            'outline' => '#A6ADB8',
            'outline-variant' => '#DDE2E8',
            'primary' => '#00897B',
            'primary-container' => '#B2DFDB',
            'on-primary' => '#FFFFFF',
            'on-primary-container' => '#00332E',
            'secondary' => '#B8790E',
            'secondary-container' => '#FFE8C7',
            'on-secondary-container' => '#6B4300',
            'tertiary-container' => '#DCE3F0',
            'on-tertiary-container' => '#424D61',
            'error' => '#D1453B',
            'error-container' => '#FBE1DE',
            'on-error-container' => '#7A1710',
            'primary-rgb' => '0, 137, 123',
            'primary-soft' => '#B2DFDB',
            'error-soft' => '#FBE1DE',
            'secondary-soft' => '#FFE8C7',
        ],
    ][$__theme];
@endphp
<style>
    :root {
        @foreach($__themeColors as $__name => $__value)
            --c-{{ $__name }}: {{ $__value }};
        @endforeach
    }
</style>
