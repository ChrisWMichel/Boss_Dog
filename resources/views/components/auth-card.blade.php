<div class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0">
    <div>
        <div class="flex items-center justify-center w-full">
            <a href="{{ route('dashboard') }}" class="block pt-2 pl-5"> 
              <img src="{{ asset('images/projectLogo-large.png') }}" alt="Logo" class="w-1/2 mx-auto" />
            </a>
          </div>
    </div>

    <div class="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md sm:max-w-md sm:rounded-lg">
        {{ $slot }}
    </div>
</div>