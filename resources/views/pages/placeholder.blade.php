@extends('layouts.dashboard')

@section('title', $title)

@section('content')
    <div class="content-panel">
        <h2 style="font-family:'Fraunces', serif;color:var(--gold);font-size:1.35rem;margin-bottom:.5rem;">
            {{ $title }}
        </h2>
        <p style="color:var(--text-muted);font-size:.9rem;">
            Module en cours de préparation.
        </p>
    </div>
@endsection
