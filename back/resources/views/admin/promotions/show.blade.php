@extends('layouts.app')
@section('title', $promotion->code)
@section('page-title', $promotion->code)
@section('content')
    <div class="card">
        <div class="card-header"><h1 class="card-title">Details promotion</h1><a class="btn btn-amber btn-sm" href="{{ route('admin.promotions.edit', $promotion) }}">Modifier</a></div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label">Nom</div><div class="detail-value">{{ $promotion->name }}</div></div>
                <div class="detail-item"><div class="detail-label">Code</div><div class="detail-value">{{ $promotion->code }}</div></div>
                <div class="detail-item"><div class="detail-label">Valeur</div><div class="detail-value">{{ $promotion->type === 'percent' ? $promotion->value.' %' : number_format($promotion->value, 0, ',', ' ').' FCFA' }}</div></div>
                <div class="detail-item"><div class="detail-label">Statut</div><div class="detail-value">{{ $promotion->is_active ? 'Active' : 'Inactive' }}</div></div>
            </div>
        </div>
    </div>
@endsection
