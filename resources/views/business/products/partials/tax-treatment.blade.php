{{-- Usage: @include('business.products.partials.tax-treatment', ['value' => $product->tax_treatment ?? null, 'taxSettings' => $taxSettings]) --}}
@php
    $taxSettings = $taxSettings ?? auth()->user()->business->getTaxSettingsOrCreate();
    $selected = old('tax_treatment', $value ?? '') ?? '';
    $rate = rtrim(rtrim(number_format((float) $taxSettings->tax_rate, 2), '0'), '.');
    $defaultLabel = $taxSettings->tax_inclusive ? 'price includes tax' : 'tax added on top';
@endphp
<div class="mb-3">
    <label class="form-label d-block">Tax Treatment</label>
    @if($taxSettings->tax_enabled)
        <div class="form-check">
            <input class="form-check-input" type="radio" name="tax_treatment" id="tax_treatment_inherit" value="" @checked($selected === '')>
            <label class="form-check-label" for="tax_treatment_inherit">
                Use business setting <small class="text-muted">({{ $taxSettings->tax_name }} {{ $rate }}%, {{ $defaultLabel }})</small>
            </label>
        </div>
        @foreach(\App\Models\Product::TAX_TREATMENTS as $key => $label)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tax_treatment" id="tax_treatment_{{ $key }}" value="{{ $key }}" @checked($selected === $key)>
                <label class="form-check-label" for="tax_treatment_{{ $key }}">{{ $label }}</label>
            </div>
        @endforeach
    @else
        <input type="hidden" name="tax_treatment" value="{{ $selected }}">
        <div class="form-text">
            Tax is currently <strong>disabled</strong> for this business, so no tax is added to sales.
            <a href="{{ route('tax.settings') }}">Enable it in Tax Settings</a> to choose per-product inclusive/exclusive pricing.
        </div>
    @endif
    @error('tax_treatment')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>
