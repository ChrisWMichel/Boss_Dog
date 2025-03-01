import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axiosClient from "../src/axiosHelper.js";
import { useToast } from "vue-toastification";

export const useProductStore = defineStore("products", () => {
    const products = ref({
        loading: false,
        data: JSON.parse(sessionStorage.getItem("products")) || [],
        // links: [],
        // from: null,
        // to: null,
        // page: 1,
        // limit: 10,
        // total: 0,
    });
    const toast = useToast();

    async function getProducts(url = null) {
        products.value.loading = true;
        url = url || "/products";
        try {
            const response = await axiosClient.get(url);
            products.value.data = response.data;
            // products.value.links = response.data.meta.links;
            // products.value.from = response.data.meta.from;
            // products.value.to = response.data.meta.to;
            // products.value.page = response.data.meta.current_page;
            // products.value.limit = response.data.meta.per_page;
            // products.value.total = response.data.meta.total;
            // console.log("Products - Pinia:", products.value);
            //sessionStorage.setItem("products", JSON.stringify(response));
            return response;
        } catch (error) {
            console.error("Get products error:", error);
            toast.error("Error getting products");
            return;
        } finally {
            products.value.loading = false;
        }
    }

    async function createProduct(data) {
        try {
            const response = await axiosClient.post("/products", data);
            products.value.data.push(response.data);
            sessionStorage.setItem(
                "products",
                JSON.stringify(products.value.data)
            );
            return response;
        } catch (error) {
            console.error("Create product error:", error);
            toast.error("Error creating product");
            return;
        }
    }

    async function updateProduct(data) {
        try {
            const response = await axiosClient.put(
                `/products/${data.id}`,
                data
            );
            const index = products.value.data.findIndex(
                (product) => product.id === data.id
            );
            products.value.data[index] = response.data;
            sessionStorage.setItem(
                "products",
                JSON.stringify(products.value.data)
            );
            return response;
        } catch (error) {
            console.error("Update product error:", error);
            toast.error("Error updating product");
            return;
        }
    }

    async function deleteProduct(id) {
        try {
            const response = await axiosClient.delete(`/products/${id}`);
            products.value = products.value.data.filter(
                (product) => product.id !== id
            );
            sessionStorage.setItem(
                "products",
                JSON.stringify(products.value.data)
            );
            return response;
        } catch (error) {
            console.error("Delete product error:", error);
            toast.error("Error deleting product");
            return;
        }
    }

    return {
        products,
        getProducts,
        createProduct,
        updateProduct,
        deleteProduct,
    };
});
