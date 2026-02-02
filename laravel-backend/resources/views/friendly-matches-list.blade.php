<x-app-layout>
    <livewire:friendly-matches-list :organization-id="auth()->user()->organization_id ?? null" />
</x-app-layout>