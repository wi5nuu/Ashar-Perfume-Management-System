<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary-apms px-4']) }}>
    {{ $slot }}
</button>
