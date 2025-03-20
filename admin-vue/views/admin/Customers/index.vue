<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold">Customers</h1>
    </div>
    <modal-create v-model="showModal" :customer="customersModel" />
    <customer-table @clickEdit="editCustomer" />
</template>

<script setup>
import { ref, onMounted } from "vue";
import customerTable from "./customerTable.vue";
import modalCreate from "./customerModal.vue";

const showModal = ref(false);
const customersModel = ref({
    id: "",
    firstname: "",
    lastname: "",
    email: "",
    is_admin: "",
});

const editCustomer = (customer) => {
    customersModel.value = customer;
    showModal.value = true;
};
</script>

<style scoped></style>
