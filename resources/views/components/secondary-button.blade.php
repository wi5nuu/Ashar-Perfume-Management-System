<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-secondary px-4']) }}>
    {{ $slot }}
</button>
