import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axiosClient from "../src/axiosHelper.js";
import { useToast } from "vue-toastification";

export const useUserStore = defineStore("user", () => {
    const user = ref({
        token: sessionStorage.getItem("TOKEN") || "",
        data: JSON.parse(sessionStorage.getItem("USER")) || {},
    });
    const toast = useToast();
    const clearForm = ref(false);

    async function login(data) {
        try {
            const response = await axiosClient.post("/login", data);
            user.value.token = response.data.token;
            user.value.data = response.data.user;
            sessionStorage.setItem("TOKEN", response.data.token);
            sessionStorage.setItem("USER", JSON.stringify(user.value.data));
            return response;
        } catch (error) {
            console.error("Login error:", error);
            toast.error("Invalid email or password");
            clearForm.value = true;
            return;
            //throw error;
        }
    }

    async function logout() {
        try {
            const response = await axiosClient.post("/logout");
            user.value.token = "";
            user.value.data = {};
            sessionStorage.removeItem("TOKEN");
            sessionStorage.removeItem("USER");
        } catch (error) {
            console.error("Logout error:", error);
            throw error;
        }
    }

    const getUser = computed(() => user.value.data);

    // const doubleCount = computed(() => count.value * 2);
    // function increment() {
    //     count.value++;
    // }

    return {
        user,
        login,
        logout,
        getUser,
        clearForm,
    };
});
