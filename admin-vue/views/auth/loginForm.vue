<template>
    <GuestLayout title="Sign into your account" @submit="login">
        <div class="text-left">
            <label for="email" class="block font-medium text-gray-900 text-sm/6"
                >Email address</label
            >
            <div class="mt-2">
                <input
                    type="email"
                    name="email"
                    id="email"
                    autocomplete="email"
                    required=""
                    class="block w-full px-3 py-2 text-base text-gray-900 bg-white rounded-md outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                />
            </div>
        </div>

        <div>
            <div
                class="flex items-center justify-between mt-10"
                style="margin-top: 20px"
            >
                <label
                    for="password"
                    class="block font-medium text-gray-900 text-sm/6"
                    >Password</label
                >
                <div class="text-sm">
                    <router-link
                        :to="{ name: 'requestPassword' }"
                        class="font-semibold text-indigo-600 hover:text-indigo-500"
                        >Forgot password?</router-link
                    >
                </div>
            </div>
            <div class="mt-2">
                <input
                    type="password"
                    name="password"
                    id="password"
                    autocomplete="current-password"
                    required=""
                    class="block w-full px-3 py-2 text-base text-gray-900 bg-white rounded-md outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                />
            </div>
        </div>

        <div
            class="py-2 mt-10 rounded-md"
            style="margin-top: 15px; background-color: #1e293b"
        >
            <button
                type="submit"
                class="flex w-full justify-center rounded-md px-3 py-1.5 text-sm/6 font-semibold text-gray-400 shadow-xs !hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
                Sign in
            </button>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "../../src/components/Layouts/GuestLayout.vue";

const login = () => {
    console.log("Login form submitted");
};
</script>

<style scoped></style>
