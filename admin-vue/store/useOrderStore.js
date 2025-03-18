import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axiosClient from "../src/axiosHelper.js";
import { useToast } from "vue-toastification";

export const useOrderStore = defineStore("orders", () => {
    const orders = ref({
        loading: false,
        ordersData: JSON.parse(sessionStorage.getItem("orders")) || [],
    });
    const toast = useToast();

    async function getOrders({
        url = null,
        per_page = 10,
        search = "",
        sortField = "",
        sortDirection = "",
    }) {
        orders.value.loading = true;
        url = url || "/orders";
        //console.log("Get orders");
        try {
            const response = await axiosClient.get(url, {
                params: {
                    search,
                    per_page: per_page,
                    sortField,
                    sortDirection,
                },
            });
            //console.log("Get orders response:", response);

            if (response && response.data) {
                orders.value.data = response.data;
                sessionStorage.setItem(
                    "orders",
                    JSON.stringify(orders.value.data)
                );
            } else {
                console.error("Unexpected response structure:", response);
                toast.error(
                    "Error getting orders: Unexpected response structure. Logout and login again."
                );
            }
            return response;
        } catch (error) {
            console.error("Get orders error:", error);
            toast.error("Error getting orders");
            return;
        } finally {
            orders.value.loading = false;
        }
    }

    async function getOrder(id) {
        try {
            const response = await axiosClient.get(`/orders/${id}`);
            //console.log("Get order response:", response.data);
            return response.data;
        } catch (error) {
            console.error("Get order error:", error);
            toast.error("Error getting order");
            return;
        }
    }

    return {
        orders,
        getOrders,
        getOrder,
    };
});
