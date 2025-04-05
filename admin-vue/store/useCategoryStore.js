import { defineStore } from "pinia";
import { ref } from "vue";
import axiosClient from "../src/axiosHelper.js";
import { useToast } from "vue-toastification";

export const useCategoryStore = defineStore("category", () => {
    const loading = ref(false);
    const error = ref(null);
    const categories = ref({loading: false, data: []});
    

    const toast = useToast();

    async function getCategories({
        url = null,
        sort_field = "",
        sort_direction = ""
    } = {}) {
        categories.value.loading = true;
        error.value = null;
        
        try {
            const response = await axiosClient.get(url || '/categories', {
                params: {
                    sort_field,
                    sort_direction
                }
            });
            categories.value = response.data;
            return categories.value;
        } catch (err) {
            console.error("Error fetching categories:", err);
            error.value = "Failed to load categories";
            return [];
        } finally {
            loading.value = false;
        }
    }

    async function createCategory(data) {
        try {
            const response = await axiosClient.post('/categories', data);
            categories.value.data.push(response.data);
            return response;
        } catch (err) {
            console.error("Error creating category:", err);
            throw err;
        } finally {
            toast.success("Category created successfully");
            loading.value = false;
        }
    }

    async function updateCategory(data) {
        try {
            const response = await axiosClient.put(`/categories/${data.id}`, data);
            const index = categories.value.data.findIndex(c => c.id === data.id);
            if (index !== -1) {
                categories.value.data[index] = response.data;
            }
            toast.success("Category updated successfully");
            return response;
        } catch (err) {
            console.error("Error updating category:", err);
            toast.error("You cannot choose category as parent which is already a child of the category.");
            toast.error(err.response.data.message);
            throw err;
        } finally {
            
            loading.value = false;
        }
    }

    async function deleteCategory(id) {
        try {
            await axiosClient.delete(`/categories/${id}`);
            categories.value.data = categories.value.data.filter(c => c.id !== id);
        } catch (err) {
            console.error("Error deleting category:", err);
            throw err;
        } finally {
            toast.success("Category deleted successfully");
            loading.value = false;
        }
    }

    return {
        categories,
        loading,
        error,
        getCategories,
        createCategory,
        updateCategory,
        deleteCategory,
    };
});