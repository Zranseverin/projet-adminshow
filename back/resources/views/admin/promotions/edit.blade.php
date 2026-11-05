@extends('layouts.app')
@section('title', 'Modifier promotion')
@section('page-title', 'Modifier promotion')
@section('content')
    <div class="card"><div class="card-header"><h1 class="card-title">{{ $promotion->code }}</h1></div><div class="card-body">
        <form method="POST" action="{{ route('admin.promotions.update', $promotion) }}">
            @include('admin.promotions._form', ['method' => 'PUT', 'submitLabel' => 'Mettre a jour'])
        </form>
    </div></div>
@endsection
