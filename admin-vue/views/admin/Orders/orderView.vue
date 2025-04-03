<template>
    <div v-if="loading" class="flex items-center justify-between mt-10 mb-3">
        <h1 class="text-3xl font-semibold">Loading..</h1>
    </div>
    <div v-else class="flex items-center justify-between mt-10 mb-3">
        <button
            @click="$router.back()"
            class="px-4 py-2 ml-10 text-white bg-indigo-500 rounded hover:bg-indigo-700 hover:text-white"
        >
            Back
        </button>
        <div class="w-20 h-10 border-gray-300">
            <orderStatusPage
                v-if="order && order.data"
                :status="order.data.status"
            />
        </div>
        <h1 class="mr-10 text-3xl font-semibold">Order #{{ order.data.id }}</h1>
    </div>

    <div v-if="loading" class="text-center">
        Loading order details...
        <spinnerAppLayoutVue />
    </div>

    <div v-else-if="order" class="p-4 bg-white rounded-lg shadow">
        <!-- Order details -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="mb-2">
                    <strong>Order Date:</strong> {{ order.data.created_at }}
                </p>
                <!-- <p>
                    <strong>Status:</strong>
                    <span
                        class="px-2 py-1 ml-2 text-white rounded"
                        :class="getStatusClass()"
                        >{{ order.data.status }}</span
                    >
                </p> -->
                <select
                    v-model="order.data.status"
                    @change="onStatusChange"
                    class="w-1/5 px-2 py-1 ml-2 border rounded"
                >
                    <option
                        v-for="status in orderStatus"
                        :key="status"
                        :value="status"
                    >
                        {{ status }}
                    </option>
                </select>
            </div>
            <div>
                <p>
                    <strong>Customer:</strong> {{ order.data.user?.firstname }}
                    {{ order.data.user?.lastname }}
                </p>
                <p><strong>Email:</strong> {{ order.data.user?.email }}</p>
            </div>
        </div>

        <!-- Order items -->
        <h2 class="mb-3 text-xl font-semibold">Order Items</h2>
        <div
            v-if="order.data.items && order.data.items.length"
            class="overflow-hidden border rounded-lg"
        >
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left"></th>
                        <th class="p-2 text-left">Product</th>
                        <th class="p-2 text-right">Price</th>
                        <th class="p-2 text-right">Quantity</th>
                        <th class="p-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in order.data.items"
                        :key="item.id"
                        class="border-t"
                    >
                        <td class="p-2">
                            <img
                                class="object-cover w-16 h-16"
                                :src="
                                    item.product?.image
                                        ? (isFullUrl(item.product.image) ? item.product.image : `/${item.product.image}`)
                                        : '/img/noimage.png'
                                "
                                :alt="item.product?.title"
                            />
                        </td>
                        <td class="p-2">{{ item.product?.title }}</td>
                        <td class="p-2 text-right">
                            <formatPrice :price="item.unit_price" />
                        </td>
                        <td class="p-2 text-right">{{ item.quantity }}</td>
                        <td class="p-2 text-right">
                            <formatPrice
                                :price="item.unit_price * item.quantity"
                            />
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr class="font-semibold border-t">
                        <td colspan="4" class="p-2 text-right">Total:</td>
                        <td class="p-2 font-extrabold text-right">
                            <formatPrice :price="order.data.total" />
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useOrderStore } from "../../../store/useOrderStore";
import { useRoute } from "vue-router";
import formatPrice from "../../../src/components/core/formatPrice.vue";
import spinnerAppLayoutVue from "../../../src/components/core/spinnerAppLayout.vue";
import axiosClient from "../../../src/axiosHelper";
import { useToast } from "vue-toastification";
import orderStatusPage from "./orderStatus.vue";

const toast = useToast();
const route = useRoute();
const orderStore = useOrderStore();
const order = ref(null);
const orderStatus = ref([]);
const loading = ref(true);

// Function to check if a URL is a full URL (starts with http:// or https://)
function isFullUrl(url) {
  if (!url) return false;
  return url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:');
}

const getStatusClass = () => {
    if (!order.value.data) return "";

    switch (order.value.data.status) {
        case "paid":
            return "bg-green-500";
        case "unpaid":
            return "bg-yellow-500";
        case "cancelled":
            return "bg-red-500";
        default:
            return "bg-gray-500";
    }
};

const onStatusChange = async () => {
    const newStatus = order.value.data.status;
    try {
        await axiosClient
            .post(
                `/orders/update-status/${order.value.data.id}/${newStatus}`,
                {},
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                }
            )
            .then((response) => {
                toast.success("Order status updated successfully");
                //console.log("Order status updated:", response.data);
            });
    } catch (error) {
        console.error("Failed to update order status:", error);
    }
};

onMounted(async () => {
    loading.value = true;
    try {
        order.value = await orderStore.getOrder(route.params.id);
        await axiosClient.get("/orders/statuses").then((response) => {
            orderStatus.value = response.data;
            //console.log("Order statuses loaded:", orderStatus.value);
        });
        // console.log("Order loaded:", order.value);
    } catch (error) {
        console.error("Failed to load order:", error);
    } finally {
        loading.value = false;
    }
});
</script>
