---
name: mojturnir-design
description: Visual design principles and cross-area theming rules for MojTurnir (which areas use which theme, how to reskin safely, empty-state/data-honesty rules). Use for any UI/styling work, not just the public+player pages.
---

# MojTurnir design principles

## Two live themes — know which one you're in

- **New teal theme** (`#57f1db` primary / `#ffb95f` secondary / `#0b0e14` base, Montserrat+Inter): public pages (`/takmicenja`, `/organizacija`, `/tim`), player pages (`/moje-lige`), landing page. Fully standalone documents, Tailwind CDN + inline config — see the `mojturnir-page` skill for the exact boilerplate.
- **Old periwinkle theme** (`#B4C0FF` accent, Manrope/Unbounded, CSS custom properties like `--bg-card`/`--text-primary` defined in `layouts/app.blade.php` and `layouts/public.blade.php`): organizer dashboard, admin panel, referee flows, semafor/projector displays. Still in active use — don't "fix" it into the teal theme unless asked; that's a much bigger job than it looks (checked: `layouts/app.blade.php` is shared by ~49 other views).

**Never touch a shared layout's own CSS-variable *values*** to reskin one page. Instead, override the same variable *names* in a page-scoped `<style>` block (pushed via `@push('styles')` into the real layout, or inlined directly in a standalone page's own `<head>`) — the override only applies to that page's render, so it can't leak into unrelated areas. This is exactly how the player pages got recolored without touching organizer/admin.

## Reskinning checklist (do all of these, in order)

1. Find every hardcoded color that *isn't* a CSS var/Tailwind token already (`grep` for hex literals and `rgba(`) — these are what actually need changing; the var-driven ones update for free once you override the var.
2. Find every inline `font-family: 'Unbounded'`/`'Manrope'`/`'Figtree'` — swap to `'Montserrat'`/`'Inter'` explicitly; a var override can't reach inline `style="font-family:..."`.
3. Re-render (see `laravel-conventions` skill) and diff the rendered HTML for the old palette's literal hex codes to confirm nothing slipped through.

## Data honesty

Never fabricate stats, trust badges/logos, testimonials, or stock-photo avatars/headshots. If a landing/dashboard section wants a number, pull it live from the DB (organizations count, active competitions, matches played, etc.) or drop the section. Real user avatars → initials-in-a-circle from their actual name, not a placeholder image URL.

## Layout rules that came from direct user feedback (don't re-litigate)

- One navigation surface per breakpoint: sidebar OR bottom-nav, never both showing the same destinations, and never a duplicate nav row inside the page content (tabs echoing the sidebar).
- Never make the user scroll a table horizontally to see all columns — shrink font/padding and demote secondary info to a sub-line instead of a new column.
- Don't increase font sizes when asked to "just fix colors/fonts" — a same-visual-weight reskin is the ask unless a layout change is explicitly requested too.
