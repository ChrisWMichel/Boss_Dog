<template>
    <div class="flex items-center justify-between mt-20 mb-3">
        <h1 class="text-3xl font-semibold">Orders</h1>
    </div>

    <orders-table @clickView="viewOrder" />
</template>

<script setup>
import { ref, onMounted } from "vue";
import OrdersTable from "./ordersTable.vue";
import { useOrderStore } from "../../../store/useOrderStore";

const orderStore = useOrderStore();

const viewOrder = (order) => {
    console.log("Order:", order);
};
</script>

<style scoped></style>
