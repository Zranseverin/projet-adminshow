@csrf
@isset($method) @method($method) @endisset

<div class="form-grid">
    <div class="form-field">
        <label class="form-label" for="reference">Reference</label>
        <input class="form-control" id="reference" name="reference" value="{{ old('reference', $order->reference) }}" placeholder="auto si vide">
        @error('reference') <div class="form-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-field">
        <label class="form-label" for="customer_name">Client</label>
        <input class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required>
        @error('customer_name') <div class="form-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-field">
        <label class="form-label" for="customer_email">Email</label>
        <input class="form-control" id="customer_email" name="customer_email" type="email" value="{{ old('customer_email', $order->customer_email) }}">
        @error('customer_email') <div class="form-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-field">
        <label class="form-label" for="customer_phone">Telephone</label>
        <input class="form-control" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}">
    </div>
    <div class="form-field full">
        <label class="form-label" for="shipping_address">Adresse livraison</label>
        <input class="form-control" id="shipping_address" name="shipping_address" value="{{ old('shipping_address', $order->shipping_address) }}">
    </div>
    <div class="form-field">
        <label class="form-label" for="shipping_city">Ville</label>
        <input class="form-control" id="shipping_city" name="shipping_city" value="{{ old('shipping_city', $order->shipping_city) }}">
    </div>
    <div class="form-field">
        <label class="form-label" for="shipping_country">Pays</label>
        <input class="form-control" id="shipping_country" name="shipping_country" value="{{ old('shipping_country', $order->shipping_country) }}">
    </div>
    <div class="form-field">
        <label class="form-label" for="status">Statut</label>
        <select class="form-control" id="status" name="status">
            @foreach (['pending' => 'En attente', 'processing' => 'En preparation', 'shipped' => 'Expediee', 'delivered' => 'Livree', 'cancelled' => 'Annulee'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $order->status ?: 'pending') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-field">
        <label class="form-label" for="payment_status">Paiement</label>
        <select class="form-control" id="payment_status" name="payment_status">
            @foreach (['unpaid' => 'Non paye', 'paid' => 'Paye', 'refunded' => 'Rembourse'] as $value => $label)
                <option value="{{ $value }}" @selected(old('payment_status', $order->payment_status ?: 'unpaid') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-field">
        <label class="form-label" for="payment_method">Methode paiement</label>
        <select class="form-control" id="payment_method" name="payment_method">
            @foreach (['card' => 'Carte bancaire', 'orange' => 'Orange Money', 'mtn' => 'MTN Mobile Money', 'moov' => 'Moov Money', 'wave' => 'Wave'] as $value => $label)
                <option value="{{ $value }}" @selected(old('payment_method', $order->payment_method ?: 'card') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-field">
        <label class="form-label" for="payment_reference">Reference paiement</label>
        <input class="form-control" id="payment_reference" name="payment_reference" value="{{ old('payment_reference', $order->payment_reference) }}">
    </div>
    <div class="form-field">
        <label class="form-label" for="paid_at">Date paiement</label>
        <input class="form-control" id="paid_at" name="paid_at" type="datetime-local" value="{{ old('paid_at', $order->paid_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="form-field">
        <label class="form-label" for="subtotal">Sous-total</label>
        <input class="form-control" id="subtotal" name="subtotal" type="number" step="0.01" min="0" value="{{ old('subtotal', $order->subtotal ?? 0) }}" required>
    </div>
    <div class="form-field">
        <label class="form-label" for="discount_total">Remise</label>
        <input class="form-control" id="discount_total" name="discount_total" type="number" step="0.01" min="0" value="{{ old('discount_total', $order->discount_total ?? 0) }}">
    </div>
    <div class="form-field">
        <label class="form-label" for="shipping_total">Livraison</label>
        <input class="form-control" id="shipping_total" name="shipping_total" type="number" step="0.01" min="0" value="{{ old('shipping_total', $order->shipping_total ?? 0) }}">
    </div>
    <div class="form-field full">
        <label class="form-label" for="notes">Notes</label>
        <textarea class="form-control" id="notes" name="notes">{{ old('notes', $order->notes) }}</textarea>
    </div>
</div>

<div class="action-row">
    <a class="btn btn-ghost" href="{{ route('admin.orders.index') }}">Annuler</a>
    <button class="btn btn-amber" type="submit">{{ $submitLabel }}</button>
</div>
