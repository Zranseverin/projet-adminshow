@extends('layouts.app')
@section('title', 'Mouvement stock')
@section('page-title', 'Mouvement stock')
@section('content')
    <div class="card">
        <div class="card-header"><h1 class="card-title">Details mouvement</h1><a class="btn btn-ghost btn-sm" href="{{ route('admin.stocks.index') }}">Retour</a></div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label">Livre</div><div class="detail-value">{{ $movement->book?->title }}</div></div>
                <div class="detail-item"><div class="detail-label">Type</div><div class="detail-value">{{ $movement->type }}</div></div>
                <div class="detail-item"><div class="detail-label">Quantite</div><div class="detail-value">{{ $movement->quantity }}</div></div>
                <div class="detail-item"><div class="detail-label">Stock</div><div class="detail-value">{{ $movement->stock_before }} -> {{ $movement->stock_after }}</div></div>
                <div class="detail-item"><div class="detail-label">Source</div><div class="detail-value">{{ $movement->source ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Utilisateur</div><div class="detail-value">{{ $movement->user?->name ?: '-' }}</div></div>
                <div class="detail-item" style="grid-column:1/-1"><div class="detail-label">Note</div><div class="detail-value">{{ $movement->note ?: '-' }}</div></div>
            </div>
        </div>
    </div>
@endsection
