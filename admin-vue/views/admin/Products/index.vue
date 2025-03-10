<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold">Products</h1>
        <button
            type="button"
            @click="showProductModal"
            class="px-4 py-2 text-white bg-indigo-500 rounded hover:bg-indigo-700 hover:text-white"
        >
            + New Product
        </button>
    </div>
    <modal-create v-model="showModal" :product="productModel" />
    <product-table @clickEdit="editProduct" />
</template>

<script setup>
import { ref, onMounted } from "vue";
import productTable from "./productTable.vue";
import modalCreate from "./ProductModal.vue";

const showModal = ref(false);
const productModel = ref({
    id: "",
    title: "",
    description: "",
    price: "",
    image: "",
});

const showProductModal = () => {
    showModal.value = true;
};

const editProduct = (product) => {
    productModel.value = product;
    showModal.value = true;
};
</script>

<style scoped></style>
