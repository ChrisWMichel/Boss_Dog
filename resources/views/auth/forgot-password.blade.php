<x-app-layout>
    <x-auth-card>
        <form action="{{ route('password.email') }}" method="post" class="w-[400px] mx-auto p-6 my-16">
            @csrf
            <h2 class="mb-5 text-2xl font-semibold text-center">
              Enter your Email to reset password 
            </h2>
            <p class="mb-6 text-center text-gray-500">
              or
              <a
                href="{{ route('login') }}"
                class="text-purple-600 hover:text-purple-500"
                >login with existing account</a
              >
            </p>
                <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Validation Errors -->
            {{-- <x-auth-validation-errors class="mb-4" :errors="$errors" /> --}}
    
            <div class="mb-3">
              <x-input
                id="loginEmail"
                type="email"
                name="email"
                :value="old('email')"
                placeholder="Your email address"
                required
                autofocus
              />
            </div>
            <button
              class="w-full btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700"
            >
              Submit
            </button>
          </form>
    </x-auth-card>
</x-app-layout>
