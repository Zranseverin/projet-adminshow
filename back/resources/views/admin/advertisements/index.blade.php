@extends('layouts.app')

@section('title', 'Publicites')
@section('page-title', 'Publicites du site')

@section('content')
    @if (session('success')) <div class="flash">{{ session('success') }}</div> @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="card-title">Gestion des publicites front</h1>
                <div class="form-help">Ces contenus alimentent la banniere accueil, les cartes sponsorisees et la pub du catalogue.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.advertisements.update') }}">
            @csrf
            @method('PUT')
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Emplacement</th>
                            <th>Texte</th>
                            <th>Image et lien</th>
                            <th>Styles</th>
                            <th>Actif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($advertisements as $advertisement)
                            <tr>
                                <td>
                                    <span class="badge badge-format">{{ $advertisement->placement }}</span>
                                    <input class="form-control" name="advertisements[{{ $advertisement->id }}][sort_order]" type="number" min="0" value="{{ old('advertisements.'.$advertisement->id.'.sort_order', $advertisement->sort_order) }}" style="margin-top:8px;max-width:90px">
                                </td>
                                <td>
                                    <input class="form-control" name="advertisements[{{ $advertisement->id }}][label]" value="{{ old('advertisements.'.$advertisement->id.'.label', $advertisement->label) }}" placeholder="Sponsorise - Marque" style="margin-bottom:8px">
                                    <input class="form-control" name="advertisements[{{ $advertisement->id }}][title]" value="{{ old('advertisements.'.$advertisement->id.'.title', $advertisement->title) }}" placeholder="Titre" required style="margin-bottom:8px">
                                    <textarea class="form-control" name="advertisements[{{ $advertisement->id }}][subtitle]" rows="3" placeholder="Description">{{ old('advertisements.'.$advertisement->id.'.subtitle', $advertisement->subtitle) }}</textarea>
                                </td>
                                <td>
                                    <input class="form-control" name="advertisements[{{ $advertisement->id }}][image_url]" type="url" value="{{ old('advertisements.'.$advertisement->id.'.image_url', $advertisement->image_url) }}" placeholder="URL de l'image" style="margin-bottom:8px">
                                    <input class="form-control" name="advertisements[{{ $advertisement->id }}][button_text]" value="{{ old('advertisements.'.$advertisement->id.'.button_text', $advertisement->button_text) }}" placeholder="Texte bouton" style="margin-bottom:8px">
                                    <input class="form-control" name="advertisements[{{ $advertisement->id }}][button_url]" type="url" value="{{ old('advertisements.'.$advertisement->id.'.button_url', $advertisement->button_url) }}" placeholder="URL bouton">
                                </td>
                                <td>
                                    <input class="form-control" name="advertisements[{{ $advertisement->id }}][background]" value="{{ old('advertisements.'.$advertisement->id.'.background', $advertisement->background) }}" placeholder="Background CSS" style="margin-bottom:8px">
                                    <input class="form-control" name="advertisements[{{ $advertisement->id }}][button_background]" value="{{ old('advertisements.'.$advertisement->id.'.button_background', $advertisement->button_background) }}" placeholder="Couleur bouton" style="margin-bottom:8px">
                                    <input class="form-control" name="advertisements[{{ $advertisement->id }}][button_color]" value="{{ old('advertisements.'.$advertisement->id.'.button_color', $advertisement->button_color) }}" placeholder="Couleur texte bouton">
                                </td>
                                <td>
                                    <input type="hidden" name="advertisements[{{ $advertisement->id }}][is_active]" value="0">
                                    <label class="checkbox-row">
                                        <input type="checkbox" name="advertisements[{{ $advertisement->id }}][is_active]" value="1" @checked(old('advertisements.'.$advertisement->id.'.is_active', $advertisement->is_active))>
                                        Visible
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="action-row" style="padding:16px 20px">
                <button class="btn btn-amber" type="submit">Enregistrer les publicites</button>
            </div>
        </form>
    </div>
@endsection
