import axios from "axios";
import { useUserStore } from "../store/useUserStore";
import router from "../vue-router";

const axiosClient = axios.create({
    baseURL: `${import.meta.env.VITE_API_URL}/api`,
    headers: {
        "Content-Type": "application/json",
    },
});

axiosClient.interceptors.request.use((config) => {
    const userStore = useUserStore();
    config.headers.Authorization = `Bearer ${userStore.user.token}`;
    return config;
});

axiosClient.interceptors.response.use(
    (response) => {
        //console.log("Response:", response);
        return response;
    },
    (error) => {
        if (error.response) {
            //console.log("Response error message:", error.response.data);
            if (error.response.status === 401) {
                // console.log(
                //     "401 Unauthorized - Removing token and redirecting to login"
                // );
                router.push({ name: "login" });
            }
        } else {
            console.error("Network or server error:", error);
        }
        //console.log("Error config:", error);
        //throw error;
    }
);

export default axiosClient;
