@extends('layouts.app')

@section('content')
    <livewire:friendly-matches-list :organization-id="auth()->user()->organization_id ?? null" />
@endsection