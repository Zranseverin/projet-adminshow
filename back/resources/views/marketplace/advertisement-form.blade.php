@extends('marketplace.layout')
@section('title', $advertisement->exists ? 'Modifier la publicité' : 'Ajouter une publicité')
@section('content')
<div class="heading"><div><h1>{{ $advertisement->exists ? 'Modifier la publicité' : 'Ajouter une publicité' }}</h1><p class="muted">L’image conserve ses proportions dans la bande publicitaire animée.</p></div><a href="{{ route('marketplace.advertisements.index') }}">Retour aux publicités</a></div>
<form class="card grid" method="post" enctype="multipart/form-data" action="{{ $advertisement->exists ? route('marketplace.advertisements.update', $advertisement) : route('marketplace.advertisements.store') }}">@csrf @if($advertisement->exists)@method('put')@endif
<div class="full"><label for="title">Titre de la publicité</label><input id="title" name="title" value="{{ old('title', $advertisement->title) }}" required maxlength="220"></div>
@if($advertisement->public_image_url)<div class="full"><img src="{{ $advertisement->public_image_url }}" alt="Image actuelle" style="max-width:100%;max-height:220px;object-fit:contain"></div>@endif
<div><label for="image">Téléverser une image</label><input id="image" name="image" type="file" accept="image/png,image/jpeg,image/webp"><small class="muted">JPG, PNG ou WebP, 4 Mo maximum.</small></div>
<div><label for="image_url">Ou utiliser une adresse d’image</label><input id="image_url" name="image_url" type="url" placeholder="https://…" value="{{ old('image_url', str_starts_with($advertisement->image_url ?? '', 'http') ? $advertisement->image_url : '') }}"><small class="muted">Un fichier téléversé est prioritaire. Laissez les deux champs vides pour conserver l’image actuelle.</small></div>
<div class="full"><label for="button_url">Lien au clic (facultatif)</label><input id="button_url" name="button_url" type="url" value="{{ old('button_url', $advertisement->button_url) }}" placeholder="https://…"><small class="muted">Sans lien, le clic agrandit simplement l’image.</small></div>
<div><label for="sort_order">Ordre d’affichage</label><input id="sort_order" name="sort_order" type="number" min="0" max="100000" required value="{{ old('sort_order', $advertisement->sort_order) }}"><small class="muted">Les nombres les plus petits sont affichés en premier.</small></div>
<div><input type="hidden" name="is_active" value="0"><label style="padding-top:32px"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $advertisement->is_active))> Afficher cette publicité</label></div>
<div class="full"><button>Enregistrer la publicité</button></div></form>
@endsection
