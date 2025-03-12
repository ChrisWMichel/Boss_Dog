<div class="mt-20" x-data="{ showFlash: true }" x-init="setTimeout(() => showFlash = false, 3000)">
    @if (session('flash_message'))
        <div x-show="showFlash" x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-full"
            class="flex items-center justify-center w-1/2 px-4 py-3 mx-auto mb-4 text-lg text-green-600 border-l-4 border-green-500 rounded-lg shadow-md bg-green-50">
            {{ session('flash_message') }}
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.text-green-600').style.display = 'none';
            }, 3000);
        </script>
    @endif
</div>
