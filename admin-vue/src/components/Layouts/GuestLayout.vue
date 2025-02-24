<template>
    <router-link :to="{ name: 'home' }" class="inline-block">
        <img
            class="mx-auto rounded-md custom-img-size"
            src="../../../src/assets/bossDogLogo.png"
            alt="Boss Man Company"
        />
    </router-link>
    <div
        class="flex flex-col justify-center flex-1 px-6 py-20 bg-gray-200 rounded-md shadow-lg w-min-full sm:px-12 md:px-16 lg:px-24 xl:px-32 shadow-dark-400"
        style="padding-top: 20px; padding-bottom: 20px"
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2
                class="mt-10 text-2xl font-bold tracking-tight text-center text-gray-900 sm:text-3xl"
            >
                {{ title }}
            </h2>
        </div>

        <div
            class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm"
            style="margin-top: 30px"
        >
            <form
                class="space-y-6"
                @submit.prevent="emit('submit')"
                method="POST"
            >
                <slot></slot>
            </form>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String,
});

const emit = defineEmits(["submit"]);
</script>

<style scoped>
.custom-img-size {
    height: 12.25rem;
    margin-bottom: 20px;
}
</style>
