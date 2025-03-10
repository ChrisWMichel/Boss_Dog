<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700']) }}>
    {{ $slot }}
</button>
