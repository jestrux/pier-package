@assets()
    <script defer src="https://unpkg.com/@alpinejs/ui@3.13.3-beta.4/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/focus@3.13.3/dist/cdn.min.js"></script>
@endassets

<div x-data="{ menuOpen: false }">
    <div x-menu x-model="menuOpen" class="relative flex items-center">
        {{ $slot }}
    </div>
</div>
