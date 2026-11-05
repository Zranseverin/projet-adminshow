@extends('layouts.app')

@section('title', 'Livraisons')
@section('page-title', 'Prix des livraisons')

@section('content')
    @if (session('success')) <div class="flash">{{ session('success') }}</div> @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="card-title">Modes et prix de livraison</h1>
                <div class="form-help">Ces montants sont utilises par le checkout du site et recalcules cote API.</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.delivery-options.update') }}">
            @csrf
            @method('PUT')
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Prix FCFA</th>
                            <th>Ordre</th>
                            <th>Actif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deliveryOptions as $option)
                            <tr>
                                <td><span class="badge badge-format">{{ $option->code }}</span></td>
                                <td>
                                    <input class="form-control" name="options[{{ $option->id }}][name]" value="{{ old('options.'.$option->id.'.name', $option->name) }}" required>
                                </td>
                                <td>
                                    <input class="form-control" name="options[{{ $option->id }}][description]" value="{{ old('options.'.$option->id.'.description', $option->description) }}">
                                </td>
                                <td>
                                    <input class="form-control" name="options[{{ $option->id }}][price]" type="number" step="1" min="0" value="{{ old('options.'.$option->id.'.price', (int) $option->price) }}" required>
                                </td>
                                <td>
                                    <input class="form-control" name="options[{{ $option->id }}][sort_order]" type="number" min="0" value="{{ old('options.'.$option->id.'.sort_order', $option->sort_order) }}">
                                </td>
                                <td>
                                    <input type="hidden" name="options[{{ $option->id }}][is_active]" value="0">
                                    <label class="checkbox-row">
                                        <input type="checkbox" name="options[{{ $option->id }}][is_active]" value="1" @checked(old('options.'.$option->id.'.is_active', $option->is_active))>
                                        Visible
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="action-row" style="padding:16px 20px">
                <button class="btn btn-amber" type="submit">Enregistrer les prix</button>
            </div>
        </form>
    </div>
@endsection
