@props(['value'])

<label {{ $attributes->merge(['class' => 'font-weight-bold text-dark']) }}>
    {{ $value ?? $slot }}
</label>
