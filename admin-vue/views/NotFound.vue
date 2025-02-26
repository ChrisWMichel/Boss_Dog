<template>
    <main
        class="grid min-h-full px-6 py-24 bg-white place-items-center sm:py-32 lg:px-8"
    >
        <div class="mt-20 text-center">
            <p class="text-3xl font-semibold text-red-600 lg:text-7xl">404</p>
            <h1
                class="mt-4 text-5xl font-semibold tracking-tight text-gray-900 text-balance sm:text-7xl"
            >
                Page not found
            </h1>
            <p
                class="mt-6 text-lg font-medium text-gray-500 text-pretty sm:text-xl/8"
            >
                Sorry, we couldn’t find the page you’re looking for.
            </p>
            <div class="flex items-center justify-center mt-10 gap-x-6">
                <router-link
                    :to="{ name: 'home' }"
                    class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-slate-300 shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >Go back home</router-link
                >

                <!-- <a href="#" class="text-sm font-semibold text-gray-900"
                    >Contact support <span aria-hidden="true">&rarr;</span></a
                > -->
            </div>
        </div>
    </main>
</template>

<script setup>
import router from "../router";
</script>

<style scoped></style>
