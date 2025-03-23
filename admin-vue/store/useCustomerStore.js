import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axiosClient from "../src/axiosHelper.js";
import { useToast } from "vue-toastification";

export const useCustomerStore = defineStore("customer", () => {
    const customer = ref({
        token: sessionStorage.getItem("TOKEN") || "",
        data: JSON.parse(sessionStorage.getItem("customer")) || {},
    });
    const customers = ref({
        loading: false,
        data: JSON.parse(sessionStorage.getItem("customers")) || [],
    });
    const toast = useToast();
    const clearForm = ref(false);

    async function getCustomers({
        url = null,
        per_page = 10,
        search = "",
        sortField = "",
        sortDirection = "",
    }) {
        customers.value.loading = true;
        url = url || "/customers";
        //console.log("Get customers");
        try {
            const response = await axiosClient.get(url, {
                params: {
                    search,
                    per_page: per_page,
                    sortField,
                    sortDirection,
                },
            });
            //console.log("Get customers response:", response.data);

            if (response && response.data) {
                customers.value.data = response.data;
                sessionStorage.setItem(
                    "customers",
                    JSON.stringify(customers.value.data)
                );
            } else {
                console.error("Unexpected response structure:", response);
                toast.error(
                    "Error getting customers: Unexpected response structure. Logout and login again."
                );
            }
            return response;
        } catch (error) {
            console.error("Get customers error:", error);
            toast.error("Error getting customers");
            return;
        } finally {
            customers.value.loading = false;
        }
    }

    async function updateCustomer(data) {
        const id = data.id;
        const url = null;
        //console.log('Sending customer data:', data);

        try {
            const customerData = {
                first_name: data.first_name,
                last_name: data.last_name,
                email: data.email,
                phone: data.phone,
                status: data.status.toString(),
            };
            
             // Include billing address if address1 is not empty
        if (data.billing_address && data.billing_address.address1) {
            customerData.billing = {
                address1: data.billing_address.address1,
                address2: data.billing_address.address2 || '',
                city: data.billing_address.city || '',
                state: data.billing_address.state || '',
                zip_code: data.billing_address.zip_code || '',
                country_code: data.billing_address.country_code || ''
            };
            
            // If shipping address is empty, use the same data as billing
            if (!data.shipping_address || !data.shipping_address.address1) {
                customerData.shipping = { ...customerData.billing };
            }
        }
            
            // Include shipping address if address1 is not empty
        if (data.shipping_address && data.shipping_address.address1) {
            customerData.shipping = {
                address1: data.shipping_address.address1,
                address2: data.shipping_address.address2 || '',
                city: data.shipping_address.city || '',
                state: data.shipping_address.state || '',
                zip_code: data.shipping_address.zip_code || '',
                country_code: data.shipping_address.country_code || ''
            };
        }
            //console.log('Sending formatted customer data:', customerData);
            const response = await axiosClient.put(`/customers/${id}`, customerData, {
                headers: {
                    "Content-Type":
                        data instanceof FormData
                            ? "multipart/form-data"
                            : "application/json",
                },
            });

            if (response && response.data) {
                const index = customers.value.data.data.findIndex(c => c.id === response.data.id);
                customers.value.data.data[index] = response.data;
                if (index !== -1) {
                    customers.value.data.data[index] = response.data;
                    sessionStorage.setItem("customers", JSON.stringify(customers.value.data));
                }
                toast.success("Customer updated successfully");
                this.getCustomers({
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
            console.error("Update customer error:", error);
            if (error.response && error.response.data) {
                console.error("Error details:", error.response.data);
            }
            toast.error("Error updating customer");
            return;
        }
    }

    async function createCustomer(data) {
        const url = null;
        try {
            const response = await axiosClient.post("/customers", data, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });

            if (response.data) {
                customers.value.data.data.push(response.data);
                sessionStorage.setItem(
                    "customers",
                    JSON.stringify(customers.value.data)
                );

                toast.success("Customer created successfully");
                this.getCustomers({
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
            console.error("Create customer error:", error);
            if (error.response && error.response.data) {
                console.error("Error details:", error.response.data);
            }
            toast.error("Error creating customer");
            return;
        }
    }

    async function deleteCustomer(id) {
        const url = null;
        try {
            const response = await axiosClient.delete(`/customers/${id}`);
            // Refetch customers to ensure state consistency
            await getCustomers({
                url,
                search: "",
                per_page: "10",
                sortField: "",
                sortDirection: "asc",
            });
            toast.success("customer deleted successfully");
        } catch (error) {
            console.error("Delete customer error:", error);
            toast.error("Error deleting customer");
            return;
        }
    }

    async function getCustomerData(id) {
        try {
            const response = await axiosClient.get(`/customers/${id}`);
            customer.value.data = response.data;
            sessionStorage.setItem("customer", JSON.stringify(response.data));
            return response;
        } catch (error) {
            console.error("Get customer error:", error);
            toast.error("Error getting customer");
            throw error;
        }
    }

    const getCustomer = computed(() => customer.value.data);

    return {
        customer,
        getCustomer,
        clearForm,
        customers,
        getCustomerData,
        getCustomers,
        updateCustomer,
        createCustomer,
        deleteCustomer,
    };
});
