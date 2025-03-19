import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axiosClient from "../src/axiosHelper.js";
import { useToast } from "vue-toastification";

export const useUserStore = defineStore("user", () => {
    const user = ref({
        token: sessionStorage.getItem("TOKEN") || "",
        data: JSON.parse(sessionStorage.getItem("USER")) || {},
    });
    const users = ref({
        loading: false,
        data: JSON.parse(sessionStorage.getItem("users")) || [],
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

    async function getUsers({
        url = null,
        per_page = 10,
        search = "",
        sortField = "",
        sortDirection = "",
    }) {
        users.value.loading = true;
        url = url || "/users";
        //console.log("Get users");
        try {
            const response = await axiosClient.get(url, {
                params: {
                    search,
                    per_page: per_page,
                    sortField,
                    sortDirection,
                },
            });
            //console.log("Get users response:", response.data);

            if (response && response.data) {
                users.value.data = response.data;
                sessionStorage.setItem(
                    "users",
                    JSON.stringify(users.value.data)
                );
            } else {
                console.error("Unexpected response structure:", response);
                toast.error(
                    "Error getting users: Unexpected response structure. Logout and login again."
                );
            }
            return response;
        } catch (error) {
            console.error("Get users error:", error);
            toast.error("Error getting users");
            return;
        } finally {
            users.value.loading = false;
        }
    }

    async function updateUser(data) {
        const id = data.id;
        const url = null;

        try {
            const response = await axiosClient.put(`/users/${id}`, data, {
                headers: {
                    "Content-Type":
                        data instanceof FormData
                            ? "multipart/form-data"
                            : "application/json",
                },
            });

            if (response && response.data) {
                const index = users.value.data.data.findIndex(
                    (user) => user.id === id
                );
                users.value.data.data[index] = response.data;
                sessionStorage.setItem(
                    "users",
                    JSON.stringify(users.value.data.data)
                );
                toast.success("User updated successfully");
                this.getUsers({
                    url,
                    search: "",
                    per_page: "10",
                    sortField: "",
                    sortDirection: "asc",
                });
            } else {
                console.error("Unexpected response structure:", response);
            }
        } catch (error) {
            console.error("Update user error:", error);
            if (error.response && error.response.data) {
                console.error("Error details:", error.response.data);
            }
            toast.error("Error updating user");
            return;
        }
    }

    async function createUser(data) {
        const url = null;
        try {
            const response = await axiosClient.post("/users", data, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });

            if (response.data) {
                users.value.data.data.push(response.data);
                sessionStorage.setItem(
                    "users",
                    JSON.stringify(users.value.data)
                );

                toast.success("User created successfully");
                this.getUsers({
                    url,
                    search: "",
                    per_page: "10",
                    sortField: "",
                    sortDirection: "asc",
                });
            } else {
                console.error("Unexpected response structure:", response);
            }

            // return response;
        } catch (error) {
            console.error("Create user error:", error);
            if (error.response && error.response.data) {
                console.error("Error details:", error.response.data);
            }
            toast.error("Error creating user");
            return;
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
        users,
        getUsers,
        updateUser,
        createUser,
    };
});
