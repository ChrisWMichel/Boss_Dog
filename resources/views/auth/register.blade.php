<x-app-layout>
    <x-auth-card>
    <form
        action="{{ route('register') }}"
        method="post"
        class="w-[400px] mx-auto p-6 my-16"
        x-data="signupForm"
        @submit.prevent="submit"
    >
        @csrf
        <h2 class="mb-4 text-2xl font-semibold text-center">Create an account</h2>
        <p class="mb-3 text-center text-gray-500">
            or
            <a
              href="{{ route('login') }}"
              class="text-sm text-purple-700 hover:text-purple-600"
              >login with existing account</a
            >
        </p>
        <div class="mb-4">
            <input
              placeholder="first name"
              type="text"
              name="firstname"
              x-model="form.firstname"
              @input="validateFirstname()"
              class="w-full border-gray-300 rounded-md focus:outline-none"
              :class="errors.firstname ? errorClasses : (form.firstname ? successClasses : defaultClasses)"
            />
            <small x-show="errors.firstname" x-text="errors.firstname" class="text-red-600"></small>
        </div>
        <div class="mb-4">
            <input
              placeholder="last name"
              type="text"
              name="lastname"
              x-model="form.lastname"
              @input="validateLastname()"
              class="w-full border-gray-300 rounded-md focus:outline-none"
              :class="errors.lastname ? errorClasses : (form.lastname ? successClasses : defaultClasses)"
            />
            <small x-show="errors.lastname" x-text="errors.lastname" class="text-red-600"></small>
        </div>
        <div class="mb-4">
            <input
              placeholder="Your Email"
              type="email"
              name="email"
              x-model="form.email"
              @input="validateEmail()"
              class="w-full border-gray-300 rounded-md focus:outline-none"
              :class="errors.email ? errorClasses : (form.email ? successClasses : defaultClasses)"
            />
            <small x-show="errors.email" x-text="errors.email" class="text-red-600"></small>
        </div>
        <div class="mb-4">
            <input
              placeholder="Password"
              type="password"
              name="password"
              x-model="form.password"
              @input="validatePassword()"
              name="password"
              type="password"
                x-model="form.password"
       class="w-full border-gray-300 rounded-md focus:outline-nones:outline-none"
              :class="errors.password ? errorClasses : (form.password ? successClasses : defaultClasses)"
            />
            <small x-show="errors.password" x-text="errors.email" class="text-red-600"></small>
        </div >
     <div class="mb-4">
            <input
              placeholder="Repeat Password"
              type="password"
              name="password_confirmation"
              x-model="form.password_repeat"
              @input="validatePasswordRepeat()"
       class="w-full border-gray-300 rounded-md focus:outline-nones:outline-none"
              :class="errors.password_repeat ? errorClasses : (form.password_repeat ? successClasses : defaultClasses)"
            />
            <small x-show="errors.password_repeat" x-text="errors.email" class="text-red-600"></small>
            
        </div>
        <button
     class="w-full btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700bg-emerald-700"
        >
            Signup
        </button>
    </form>
    </x-auth-card>
</x-app-layout>
