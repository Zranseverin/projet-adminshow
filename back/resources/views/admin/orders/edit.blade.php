@extends('layouts.app')
@section('title', 'Modifier commande')
@section('page-title', 'Modifier commande')
@section('content')
    <div class="card"><div class="card-header"><h1 class="card-title">{{ $order->reference }}</h1></div><div class="card-body">
        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
            @include('admin.orders._form', ['method' => 'PUT', 'submitLabel' => 'Mettre a jour'])
        </form>
    </div></div>
@endsection
