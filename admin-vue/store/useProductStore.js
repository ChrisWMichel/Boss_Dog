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
        try {
            const response = await axiosClient.get(url, {
                params: {
                    search,
                    per_page: per_page,
                    sortField,
                    sortDirection,
                },
            });

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

    async function createProduct(originalData) {
        const url = null;
        let processedData;

        try {
            // Check if we have images to upload
            if (originalData.images && originalData.images.length > 0) {
                processedData = new FormData();

                // Append each image individually
                for (let i = 0; i < originalData.images.length; i++) {
                    processedData.append("images[]", originalData.images[i]);
                }

                // Append other product data
                processedData.append("title", originalData.title);
                processedData.append("description", originalData.description || '');
                processedData.append("price", originalData.price);
                processedData.append("published", originalData.published ? "1" : "0");
                processedData.append("quantity", originalData.quantity || "");
            } else if (originalData.image instanceof File) {
                // Legacy support for single image upload
                processedData = new FormData();
                processedData.append("image", originalData.image);
                processedData.append("title", originalData.title);
                processedData.append("description", originalData.description || '');
                processedData.append("price", originalData.price);
                processedData.append("published", originalData.published ? "1" : "0");
                processedData.append("quantity", originalData.quantity || "");
            } else {
                // No images, just send JSON data
                processedData = {
                    title: originalData.title,
                    description: originalData.description || '',
                    price: originalData.price,
                    published: originalData.published ? true : false,
                    quantity: originalData.quantity || null
                };
            }

            const response = await axiosClient.post("/products", processedData, {
                headers: {
                    "Content-Type": processedData instanceof FormData
                        ? "multipart/form-data"
                        : "application/json",
                },
            });

            if (response.data) {
                products.value.data.data.push(response.data);
                sessionStorage.setItem(
                    "products",
                    JSON.stringify(products.value.data)
                );

                //toast.success("Product created successfully");
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

    async function updateProduct(originalData) {
        const id = originalData.id;
        const url = null;
        let processedData;

        // Always use FormData for updates to handle image positions and deletions
        processedData = new FormData();

        // Check if we have any new images to upload
        let hasNewImages = false;
        if (originalData.images && originalData.images.length > 0) {
            // Append each image individually
            for (let i = 0; i < originalData.images.length; i++) {
                const image = originalData.images[i];
                // Only append if it's a File object
                if (image instanceof File) {
                    processedData.append("images[]", image);
                    hasNewImages = true;
                }
            }
        }

        // If no new images were added, add a flag to indicate we're not updating images
        if (!hasNewImages) {
            processedData.append("no_new_images", "1");
        }

            // Append deleted images if any
            if (originalData.deleted_images && originalData.deleted_images.length > 0) {
                for (let i = 0; i < originalData.deleted_images.length; i++) {
                    const imageInfo = originalData.deleted_images[i];
                    if (typeof imageInfo === 'object' && imageInfo.id) {
                        processedData.append("deleted_images[]", imageInfo.id);
                        processedData.append("deleted_filenames[]", imageInfo.filename);
                    } else {
                        processedData.append("deleted_images[]", imageInfo);
                    }
                }
            }

            // Append image positions if any
            if (originalData.image_positions) {
                for (const [posId, position] of Object.entries(originalData.image_positions)) {
                    processedData.append(`image_positions[${posId}]`, position);
                }
            }

            // Append other product data
            processedData.append("title", originalData.title);
            processedData.append("description", originalData.description || '');
            processedData.append("price", originalData.price);
            processedData.append("published", originalData.published ? "1" : "0");
            processedData.append("quantity", originalData.quantity || "");
            processedData.append("_method", "PUT");

            // Append categories if any
            if (originalData.categories && originalData.categories.length > 0) {
                for (let i = 0; i < originalData.categories.length; i++) {
                    processedData.append("categories[]", originalData.categories[i]);
                }
            }

        // We're always using FormData now

        try {
            const response = await axiosClient.post(`/products/${id}`, processedData, {
                headers: {
                    "Content-Type":
                        processedData instanceof FormData
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
