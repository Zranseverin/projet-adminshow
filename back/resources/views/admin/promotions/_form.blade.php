@php $isActive = old('is_active', $promotion->exists ? $promotion->is_active : true); @endphp
@csrf
@isset($method) @method($method) @endisset

<div class="form-grid">
    <div class="form-field">
        <label class="form-label" for="name">Nom</label>
        <input class="form-control" id="name" name="name" value="{{ old('name', $promotion->name) }}" required>
    </div>
    <div class="form-field">
        <label class="form-label" for="code">Code</label>
        <input class="form-control" id="code" name="code" value="{{ old('code', $promotion->code) }}" placeholder="auto si vide">
        @error('code') <div class="form-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-field">
        <label class="form-label" for="type">Type</label>
        <select class="form-control" id="type" name="type">
            <option value="percent" @selected(old('type', $promotion->type ?: 'percent') === 'percent')>Pourcentage</option>
            <option value="fixed" @selected(old('type', $promotion->type) === 'fixed')>Montant fixe</option>
        </select>
    </div>
    <div class="form-field">
        <label class="form-label" for="value">Valeur</label>
        <input class="form-control" id="value" name="value" type="number" step="0.01" min="0" value="{{ old('value', $promotion->value ?? 0) }}" required>
    </div>
    <div class="form-field">
        <label class="form-label" for="minimum_amount">Minimum commande</label>
        <input class="form-control" id="minimum_amount" name="minimum_amount" type="number" step="0.01" min="0" value="{{ old('minimum_amount', $promotion->minimum_amount) }}">
    </div>
    <div class="form-field">
        <label class="form-label" for="usage_limit">Limite d'utilisation</label>
        <input class="form-control" id="usage_limit" name="usage_limit" type="number" min="1" value="{{ old('usage_limit', $promotion->usage_limit) }}">
    </div>
    <div class="form-field">
        <label class="form-label" for="starts_at">Debut</label>
        <input class="form-control" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', $promotion->starts_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="form-field">
        <label class="form-label" for="expires_at">Expiration</label>
        <input class="form-control" id="expires_at" name="expires_at" type="datetime-local" value="{{ old('expires_at', $promotion->expires_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="form-field">
        <input type="hidden" name="is_active" value="0">
        <label class="checkbox-row"><input type="checkbox" name="is_active" value="1" @checked((bool) $isActive)> Active</label>
    </div>
</div>
<div class="action-row">
    <a class="btn btn-ghost" href="{{ route('admin.promotions.index') }}">Annuler</a>
    <button class="btn btn-amber" type="submit">{{ $submitLabel }}</button>
</div>
