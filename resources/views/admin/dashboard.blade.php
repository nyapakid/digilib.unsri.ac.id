@extends('admin.layout')

@section('title', 'Dashboard Admin Digilib')
@section('heading', 'Dashboard')

@section('content')
    <div class="dashboard-grid">
        @foreach($cards as $card)
            <a class="metric-card" href="{{ route('admin.content.index', $card['type']) }}">
                <span>{{ $card['label'] }}</span>
                <strong>{{ $card['count'] }}</strong>
            </a>
        @endforeach
    </div>
@endsection
