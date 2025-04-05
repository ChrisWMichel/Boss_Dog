<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">Products</h1>
    <router-link
      :to="{ name: 'app.products.create' }"
      class="px-4 py-2 text-white bg-indigo-500 rounded hover:bg-indigo-700 hover:text-white"
    >
      + New Product
    </router-link>
  </div>
  <product-table @clickEdit="editProduct" />
</template>

<script setup>
import { ref, onMounted } from "vue";
import productTable from "./productTable.vue";
import ProductForm from "./productForm.vue";

const productModel = ref({
  id: "",
  title: "",
  description: "",
  price: "",
  image: "",
});

const editProduct = (product) => {
  productModel.value = product;
};
</script>

<style scoped></style>
