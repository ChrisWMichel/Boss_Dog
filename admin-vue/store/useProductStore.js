import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axiosClient from "../src/axiosHelper.js";
import { useToast } from "vue-toastification";

export const useProductStore = defineStore("products", () => {
    const products = ref({
        loading: false,
        data: JSON.parse(sessionStorage.getItem("products")) || [],
    });
    const toast = useToast();

    async function getProducts({
        url = null,
        per_page = 10,
        search = "",
        sortField = "",
        sortDirection = "",
    }) {
        products.value.loading = true;
        url = url || "/products";

        try {
            const response = await axiosClient.get(url, {
                params: {
                    search,
                    per_page: per_page,
                    sortField,
                    sortDirection,
                },
            });
            //console.log("Get products response:", response.data);

            products.value.data = response.data;
            sessionStorage.setItem(
                "products",
                JSON.stringify(products.value.data)
            );
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
        const url = null;
        try {
            if (data.image instanceof File) {
                const form = new FormData();
                form.append("image", data.image);
                form.append("title", data.title);
                form.append("description", data.description);
                form.append("price", data.price);
                data = form;
            }

            const response = await axiosClient.post("/products", data, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });

            if (response.data) {
                products.value.data.data.push(response.data);
                sessionStorage.setItem(
                    "products",
                    JSON.stringify(products.value.data)
                );

                toast.success("Product created successfully");
                this.getProducts({
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
            console.error("Create product error:", error);
            if (error.response && error.response.data) {
                console.error("Error details:", error.response.data);
            }
            toast.error("Error creating product");
            return;
        }
    }

    async function updateProduct(data) {
        const id = data.id;
        if (data.image instanceof File) {
            const form = new FormData();
            form.append("image", data.image);
            form.append("title", data.title);
            form.append("description", data.description);
            form.append("price", data.price);
            form.append("_method", "PUT");
            data = form;
        } else {
            data._method = "PUT";
        }
        try {
            const response = await axiosClient.post(`/products/${id}`, data);
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
