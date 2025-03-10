<x-app-layout>
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />
  <x-auth-card>           
    <form action="" method="post" class="w-[400px] mx-auto p-6 my-16">
        @csrf
        <h2 class="mb-5 text-2xl font-semibold text-center">
          Login to your account
        </h2>
        <p class="mb-6 text-center text-gray-500">
          or
          <a
            href="{{ route('register') }}"
            class="text-sm text-purple-700 hover:text-purple-600"
            >create new account</a
          >
        </p>


    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-4" :errors="$errors" />
    
        <div class="mb-4">
          <x-input
            id="loginEmail"
            type="email"
            name="email"
            :value="old('email')"
            :errors="$errors"
            placeholder="Your email address"
          />
        </div>
        <div class="mb-4">
          <x-input
            id="loginPassword"
            type="password"
            name="password"
            placeholder="Your password"
          />
        </div>

        <div class="flex items-center justify-between mb-5">
          <div class="flex items-center">
            <input
              id="loginRememberMe"
              type="checkbox"
              class="mr-3 text-purple-500 border-gray-300 rounded focus:ring-purple-500"
            />
            <label for="loginRememberMe">Remember Me</label>
          </div>
          @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-sm text-purple-700 hover:text-purple-600">
              Forgot Password?
          </a>
      @endif
        </div>
        <button
          class="w-full btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700"
        >
          Login
        </button>
      </form>
  </x-auth-card>
</x-app-layout>
