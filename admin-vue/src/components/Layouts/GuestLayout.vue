<template>
    <router-link :to="{ name: 'home' }" class="block mx-auto mt-10">
        <img
            class="mx-auto rounded-md custom-img-size"
            src="../../../src/assets/bossDogLogo.png"
            alt="Boss Man Company"
        />
    </router-link>
    <div
        class="w-1/2 px-3 mx-auto bg-gray-200 rounded-md shadow-lg sm:px-12 md:px-16 lg:px-24 xl:px-32 shadow-dark-900"
        style="padding-top: 20px; padding-bottom: 20px"
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-sm lg:max-w-lg">
            <h2
                class="mt-10 text-2xl font-bold tracking-tight text-center text-gray-900 sm:text-3xl"
            >
                {{ title }}
            </h2>
        </div>

        <div
            class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm lg:max-w-lg"
            style="margin-top: 30px"
        >
            <form @submit.prevent="emit('submit')" method="POST">
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
.guest-layout {
    margin: 0;
    display: flex;
    place-items: center;
    min-width: 320px;
}
</style>
