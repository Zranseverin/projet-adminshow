@extends('layouts.app')
@section('title', 'Mouvement stock')
@section('page-title', 'Mouvement stock')
@section('content')
    <div class="card">
        <div class="card-header"><h1 class="card-title">Ajouter un mouvement de stock</h1></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.stocks.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-field">
                        <label class="form-label" for="book_id">Livre</label>
                        <select class="form-control" id="book_id" name="book_id" required>
                            @foreach ($books as $book)
                                <option value="{{ $book->id }}" @selected((int) request('book_id') === $book->id)>{{ $book->title }} - stock: {{ $book->stock_quantity }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="type">Type</label>
                        <select class="form-control" id="type" name="type">
                            <option value="in">Entree</option>
                            <option value="out">Sortie</option>
                            <option value="adjustment">Ajustement positif</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="quantity">Quantite</label>
                        <input class="form-control" id="quantity" name="quantity" type="number" min="1" value="1" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="source">Source</label>
                        <input class="form-control" id="source" name="source" placeholder="Fournisseur, vente, correction...">
                    </div>
                    <div class="form-field full">
                        <label class="form-label" for="note">Note</label>
                        <textarea class="form-control" id="note" name="note"></textarea>
                    </div>
                </div>
                <div class="action-row">
                    <a class="btn btn-ghost" href="{{ route('admin.stocks.index') }}">Annuler</a>
                    <button class="btn btn-amber" type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection
