---
name: mojturnir-page
description: Design system and page-shell conventions for MojTurnir's public and player-facing pages (teal/dark Tailwind theme, sidebar/topbar/bottom-nav shell, route naming). Use when creating or editing any page under /takmicenja, /organizacija, /grad, /tim, /moje-lige, or the landing page.
---

# MojTurnir page conventions

Standalone pages (no shared Blade layout) — one file per page, own `<head>`, no build step (Tailwind via CDN + inline config). Follow this exactly; don't re-derive it.

## Head boilerplate (copy verbatim, adjust `<title>`)

```html
<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>... - MojTurnir</title>
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#0b0e14">
<link rel="apple-touch-icon" href="/icons/icon-192.svg">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Inter:wght@400;600&display=swap">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap">
<script>
    tailwind.config = {
        darkMode: "class",
        theme: { extend: {
            colors: {
                "surface-container-lowest": "#0b0e14", "surface-dim": "#10131a", "surface": "#10131a",
                "surface-container-low": "#191c22", "surface-container": "#1d2026", "surface-container-high": "#272a31",
                "surface-container-highest": "#32353c", "surface-variant": "#32353c", "surface-bright": "#363940",
                "on-surface": "#e1e2eb", "on-surface-variant": "#bacac5", "outline": "#859490", "outline-variant": "#3c4a46",
                "primary": "#57f1db", "primary-container": "#2dd4bf", "on-primary": "#003731", "on-primary-container": "#00574d",
                "secondary": "#ffb95f", "secondary-container": "#ee9800", "on-secondary-container": "#5b3800",
                "tertiary-container": "#b3bed5", "on-tertiary-container": "#424d61", "error": "#ffb4ab",
            },
            borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
            spacing: { gutter: "24px", "margin-mobile": "16px", "sidebar-width": "260px", "container-max": "1280px", base: "8px" },
            fontFamily: {
                display: ["Montserrat"], "headline-md": ["Montserrat"], "headline-lg-mobile": ["Montserrat"], "headline-lg": ["Montserrat"],
                "body-md": ["Inter"], "body-sm": ["Inter"], "body-lg": ["Inter"], "label-bold": ["Inter"],
            },
            fontSize: {
                display: ["40px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "800" }],
                "headline-md": ["20px", { lineHeight: "1.3", fontWeight: "700" }],
                "headline-lg-mobile": ["22px", { lineHeight: "1.2", fontWeight: "700" }],
                "headline-lg": ["28px", { lineHeight: "1.2", letterSpacing: "-0.01em", fontWeight: "700" }],
                "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                "label-bold": ["12px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }],
            },
        } },
    }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    body { background-color: #0b0e14; color: #e1e2eb; overflow-x: hidden; -webkit-tap-highlight-color: transparent; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #10131a; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #32353c; border-radius: 10px; }
</style>
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen pb-24 lg:pb-8">
```

Bump `display` fontSize to `48px` only on pages with no hero image competing for space (e.g. dashboard). Never bump other pages without being asked.

## Page shell (every page has exactly one of each)

1. **Sidebar** `hidden lg:flex w-sidebar-width h-screen fixed left-0 top-0 border-r border-outline-variant bg-surface-container-low` — logo, nav links (one highlighted via `text-primary border-l-4 border-primary bg-primary/5 font-label-bold`, others `text-on-surface-variant hover:text-on-surface hover:bg-surface-variant/50`), then Nalog/Odjava at the bottom.
2. **Mobile top bar** `lg:hidden sticky top-0 z-40 bg-surface border-b border-outline-variant h-16` — logo/title + utility icons only, no nav links.
3. **Desktop top bar** `hidden lg:flex ... w-[calc(100%-260px)] ml-[260px] h-16 fixed top-0` — utility icons only (settings/avatar). **Never duplicate the sidebar's nav links here** — one nav location per breakpoint, no exceptions (user has explicitly complained about this before).
4. **Main** `lg:ml-[260px] lg:mt-16 p-margin-mobile lg:p-gutter min-h-screen`.
5. **Mobile bottom nav** `lg:hidden fixed bottom-0 ... pb-[env(safe-area-inset-bottom)] bg-surface-container-high rounded-t-xl border-t` — mirrors the sidebar's items, active one gets `bg-primary-container text-on-primary-container rounded-full px-4 py-1`.

No in-page tab rows duplicating sidebar/bottom-nav destinations.

## Mobile-bleed card pattern

Cards go edge-to-edge on mobile, bordered/rounded on desktop: `-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-4 lg:p-6`. Tables use `text-sm` with tight `px-2/py-2` cells — never let a table need horizontal scroll; put secondary info (e.g. club name) as a `text-xs text-on-surface-variant` line under the primary line, not another column.

## Color semantics (don't invent new ones)

Win/positive/success → `primary` (teal). Live/scheduled/pending → `secondary` (orange), with `animate-pulse` for live. Loss/error/destructive → `error`. Never use raw Tailwind reds/greens/purples (`text-red-400` etc.) — always the named tokens above.

## Reusable partials (check these before writing new markup)

- `public.leagues._competition-body` — full competition page body (hero, standings table, schedule, stats/org sidebar OR tournament groups). Needs `$competition`, `$organization`, `$playerGroupSeeding`, `$playerPositionSeeding` (get these from `App\Services\CompetitionShowData::load($competition)`). Used by both the public and player competition-show pages — edit once, both get it.
- `public.leagues._tournament` — group standings/knockout bracket for `type === 'tournament'`.
- `player.partials.theme` — CSS-var overrides so the legacy `var(--bg-card)`-style partials (rare now) render in the current palette when included from a standalone page's `<head>`.

## Routes

Public competition/org browsing lives under route name `competitions.*` (index/show/organization/by-city/semafor/matches.*/team-matches.show), URIs are Bosnian (`/takmicenja`, `/organizacija/{org}`, `/grad/{city}`). Team profile is `teams.*` at `/tim/{team}`. AJAX/embed under `api.*`/`embed.*`. Player-area routes are `player.*` at `/moje-lige/...`. Don't invent new prefixes — extend these.

## Real data only

Never fabricate stats, testimonials, trust badges, or placeholder avatars (no stock-photo URLs). Pull real counts/aggregates from the DB (see `routes/web.php` home route for the pattern) or omit the element.
