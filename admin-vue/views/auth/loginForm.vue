<template>
    <GuestLayout title="Sign into your account" @submit="login">
        <div class="text-left">
            <label for="email" class="block font-medium text-gray-900 text-sm/6"
                >Email address</label
            >
            <div class="mt-2">
                <input
                    v-model="user.email"
                    type="email"
                    name="email"
                    id="email"
                    required
                    autocomplete="email"
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
                    v-model="user.password"
                    type="password"
                    name="password"
                    id="password"
                    required
                    autocomplete="current-password"
                    class="block w-full px-3 py-2 text-base text-gray-900 bg-white rounded-md outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                />
            </div>
        </div>
        <!-- remember me -->
        <div class="flex items-center justify-between my-3">
            <div class="flex items-center">
                <input
                    v-model="user.remember_me"
                    id="remember_me"
                    name="remember_me"
                    type="checkbox"
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                />
                <label
                    for="remember_me"
                    class="block ml-2 text-gray-900 text-sm/6"
                    >Remember me</label
                >
            </div>
        </div>

        <div
            class="py-2 mt-10 rounded-md"
            style="margin-top: 15px; background-color: #1e293b"
        >
            <button
                :disabled="loading"
                type="submit"
                class="flex w-full justify-center rounded-md px-3 py-1.5 text-sm/6 font-semibold text-gray-400 shadow-xs !hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                :class="{
                    'bg-indigo-600': !loading,
                    'cursor-not-allowed': loading,
                }"
            >
                <spinner v-if="loading" />
                <span v-else>Sign in</span>
            </button>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "../../src/components/Layouts/GuestLayout.vue";
import { ref, watch } from "vue";
import { useUserStore } from "../../store/useUserStore";
import { useRouter } from "vue-router";
import spinner from "../../src/components/core/spinner.vue";

let loading = ref(false);
let errorMsg = ref(null);
const userStore = useUserStore();
const router = useRouter();

const user = ref({
    email: "",
    password: "",
    remember_me: false,
});

const login = () => {
    loading.value = true;
    userStore
        .login(user.value)
        .then(() => {
            loading.value = false;
            router.push({ name: "app.dashboard" });
        })
        .catch((response) => {
            //debugger;
            console.log("Catch block");

            loading.value = false;
        });
};

watch(
    () => userStore.clearForm,
    (value) => {
        if (value) {
            user.value.email = "";
            user.value.password = "";
            user.value.remember_me = false;
            userStore.clearForm = false;
        }
    }
);
</script>

<style scoped></style>
