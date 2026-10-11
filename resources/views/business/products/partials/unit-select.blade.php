{{-- Usage: @include('business.products.partials.unit-select', ['name' => 'measurement_unit', 'value' => $product->measurement_unit ?? null, 'id' => 'measurement_unit', 'size' => 'form-control-lg']) --}}
@php
    $name = $name ?? 'measurement_unit';
    $id = $id ?? preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
    $selected = old($name, $value ?? 'piece') ?: 'piece';
    $size = $size ?? '';
@endphp
<select class="form-control {{ $size }} @error($name) is-invalid @enderror" id="{{ $id }}" name="{{ $name }}">
    @foreach(\App\Models\Product::UNITS as $key => $label)
        <option value="{{ $key }}" @selected($selected === $key)>{{ $label }}</option>
    @endforeach
</select>
@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
