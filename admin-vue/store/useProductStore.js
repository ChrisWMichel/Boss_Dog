import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axiosClient from "../src/axiosHelper.js";
import { useToast } from "vue-toastification";

export const useProductStore = defineStore("products", () => {
    const products = ref({
        loading: false,
        data: JSON.parse(sessionStorage.getItem("products")) || [],
    });
    const product = ref({});
    const toast = useToast();

    async function getProductData(id) {
        try {
            const response = await axiosClient.get(`/products/${id}`);
            product.value.data = response.data;
            sessionStorage.setItem("product", JSON.stringify(response.data));
            return response.data.data;
        } catch (error) {
            console.error("Get product error:", error);
            toast.error("Error getting product");
            throw error;
        }
    }

    async function getProducts({
        url = null,
        per_page = 10,
        search = "",
        sortField = "",
        sortDirection = "",
    }) {
        products.value.loading = true;
        url = url || "/products";
        //console.log("Get products");
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

            if (response && response.data) {

                products.value.data = response.data;
                sessionStorage.setItem(
                    "products",
                    JSON.stringify(products.value.data)
                );
            } else {
                console.error("Unexpected response structure:", response);
                toast.error(
                    "Error getting products: Unexpected response structure. Logout and login again."
                );
            }
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
                form.append("published", data.published ? "1" : "0");
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
        const url = null;
        const imageUpdated = data.imageUpdated || false;

    const hasValidImage = data.image instanceof File && data.image.size > 0;

    if (hasValidImage && imageUpdated) {
        const form = new FormData();
        form.append("image", data.image);
        form.append("title", data.title);
        form.append("description", data.description);
        form.append("price", data.price);
        form.append("published", data.published ? "1" : "0");
        form.append("imageUpdated", "1"); 
        form.append("_method", "PUT");
        data = form;

    } else {
        const processedData = {
            _method: "PUT",
            title: data.title,
            description: data.description,
            price: data.price,
            published: data.published ? 1 : 0,
            imageUpdated: imageUpdated ? 1 : 0, // Include the flag even for JSON requests
        };
        data = processedData; 
    }
        try {
           // console.log('Sending product data:', data);
            const response = await axiosClient.post(`/products/${id}`, data, {
                headers: {
                    "Content-Type":
                        data instanceof FormData
                            ? "multipart/form-data"
                            : "application/json",
                },
            });

            if (response && response.data) {
                const index = products.value.data.data.findIndex(
                    (product) => product.id === id
                );
                products.value.data.data[index] = response.data;
                sessionStorage.setItem(
                    "products",
                    JSON.stringify(products.value.data.data)
                );
                toast.success("Product updated successfully"); // Corrected success message
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
        } catch (error) {
            console.error("Update product error:", error);
            if (error.response && error.response.data) {
                console.error("Error status:", error.response.status);
                console.error("Error details:", error.response.data);
                if (error.response.data.errors) {
                    console.error("Validation errors:", error.response.data.errors);
                }
            }
            toast.error("Error updating product");
            return;
        }
    }

    async function deleteProduct(id) {
        const url = null;
        try {
            const response = await axiosClient.delete(`/products/${id}`);
            // Refetch products to ensure state consistency
            await getProducts({
                url,
                search: "",
                per_page: "10",
                sortField: "",
                sortDirection: "asc",
            });
            toast.success("Product deleted successfully");
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
        getProductData,
    };
});
