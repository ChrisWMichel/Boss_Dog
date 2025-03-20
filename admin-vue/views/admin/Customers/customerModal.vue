<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-10">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div
                    class="flex items-center justify-center min-h-full p-4 text-center"
                >
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl"
                        >
                            <DialogTitle
                                as="h3"
                                class="text-lg font-medium leading-6 text-gray-900"
                            >
                                Update Customer: {{ customer.id }}
                            </DialogTitle>
                            <spinner
                                v-if="customer.loading"
                                class="flex items-center justify-center"
                            />

                            <form v-else @submit.prevent="onSubmit">
                                <div class="mt-2">
                                    <custom-input
                                        v-model="customer.first_name"
                                        label="First Name"
                                        type="text"
                                        id="first_name"
                                        class="mb-2"
                                    />
                                    <custom-input
                                        type="text"
                                        class="mb-2"
                                        label="Last Name"
                                        id="last_name"
                                        v-model="customer.last_name"
                                    />
                                    <custom-input
                                        type="text"
                                        class="mb-2"
                                        v-model="customer.email"
                                        label="Email"
                                    />
                                    <custom-input
                                        type="text"
                                        class="mb-2"
                                        v-model="customer.phone"
                                        label="Phone"
                                    />
                                    <!-- <custom-input
                                        type="text"
                                        class="mb-2"
                                        v-model="customer.status"
                                        label="Status"
                                    /> -->
                                    <div class="flex items-center mb-4">
                                        <custom-input
                                            v-model="isCustomerActive"
                                            label="Status"
                                            type="checkbox"
                                        />
                                        <label for="status" class="ml-2 text-sm font-medium text-gray-700">
            Active Customer
        </label>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 gap-2 md:grid-cols-2"
                                    >
                                        <h2 class="mb-5 text-xl font-semibold">
                                            Billing Address
                                        </h2>
                                        <div class="flex items-center justify-end">
            <label class="inline-flex items-center cursor-pointer">
                <input 
                    type="checkbox" 
                    v-model="showBillingAddress" 
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                />
                <span class="ml-2 text-sm text-gray-700">Show shipping address</span>
            </label>
        </div>
                                        </div>
                                        <div
                                        v-if="showBillingAddress"
                                            class="grid grid-cols-1 gap-2 md:grid-cols-2"
                                        >
                                            <custom-input
                                                type="text"
                                                v-model="customer.billing_address.address1"
                                                label="Address 1"
                                            />
                                            <custom-input
                                                type="text"
                                                v-model="customer.billing_address.address"
                                                label="Address 2"
                                            />
                                            <custom-input
                                                type="text"
                                                v-model="customer.billing_address.city"
                                                label="City"
                                            />
                                            <custom-input
                                                type="text"
                                                v-model="customer.billing_address.state"
                                                label="State"
                                            />
                                            <custom-input
                                                type="text"
                                                v-model="customer.billing_address.zip_code"
                                                label="Zipcode"
                                            />
                                            <custom-input
                                                type="text"
                                                v-model="customer.billing_address.country_code"
                                                label="Country Code"
                                            />
                                            
                                        </div>
                                        <div
                                            class="grid grid-cols-1 gap-2 md:grid-cols-2"
                                        >
                                            <h2 class="mb-5 text-xl font-semibold">
                                                Shipping Address
                                            </h2>
                                            <div class="flex items-center justify-end">
            <label class="inline-flex items-center cursor-pointer">
                <input 
                    type="checkbox" 
                    v-model="showShippingAddress" 
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                />
                <span class="ml-2 text-sm text-gray-700">Show shipping address</span>
            </label>
        </div>
                                        </div>
                                        <div
                                         v-if="showShippingAddress"
                                            class="grid grid-cols-1 gap-2 md:grid-cols-2"
                                        >
                                            <custom-input
                                                type="text"
                                                v-model="customer.shipping_address.address1"
                                                label="Address 1"
                                            />
                                            <custom-input
                                                type="text"
                                                v-model="customer.shipping_address.address"
                                                label="Address 2"
                                            />
                                            <custom-input
                                                type="text"
                                                v-model="customer.shipping_address.city"
                                                label="City"
                                            />
                                            <custom-input
                                                type="text"
                                                v-model="customer.shipping_address.state"
                                                label="State"
                                            />
                                            <custom-input
                                                type="text"
                                                v-model="customer.shipping_address.zip_code"
                                                label="Zipcode"
                                            />
                                            <custom-input
                                                type="text"
                                                v-model="customer.shipping_address.country_code"
                                                label="Country Code"
                                            />
                                        </div>
                                </div>
                                <footer
                                    class="flex flex-col-reverse px-4 py-3 bg-gray-50 sm:px-6 sm:flex-row-reverse"
                                >
                                    <button
                                        :disabled="customer.loading"
                                        type="submit"
                                        class="px-4 py-2 mt-2 text-white bg-indigo-500 rounded hover:bg-indigo-700 hover:text-white sm:ml-2 sm:mt-0"
                                    >
                                        Submit
                                    </button>
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="px-4 py-2 text-white bg-gray-500 rounded hover:bg-gray-700 hover:text-white"
                                    >
                                        Cancel
                                    </button>
                                </footer>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from "@headlessui/vue";
import { useCustomerStore } from "../../../store/useCustomerStore";
import customInput from "../../../src/components/core/customInput.vue";
import spinner from "../../../src/components/core/spinner.vue";

const customerStore = useCustomerStore();
const showShippingAddress = ref(false);
const showBillingAddress = ref(false);

const props = defineProps({
    modelValue: Boolean,
    customer: {
        type: Object,
        default: () => ({}),
    },
});
//console.log("customerModal:", props.customer);

const isCustomerActive = computed({
    get: () => customer.value.status === 'active',
    set: (value) => {
        customer.value.status = value ? 'active' : 'disabled';
    }
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const customer = ref({
    id: props.customer.id || "",
    first_name: props.customer.first_name || "",
    last_name: props.customer.last_name || "",
    email: props.customer.email || "",
    phone: props.customer.phone || "",
    status: props.customer.status || "",
    billing_address: props.customer.billing_address || {
        address1: "",
        address2: "",
        city: "",
        state: "",
        zip_code: "",
        country_code: ""
    },
    shipping_address: props.customer.shipping_address || {
        address1: "",
        address2: "",
        city: "",
        state: "",
        zip_code: "",
        country_code: ""
    }
});

const emit = defineEmits(["update:modelValue", "close"]);

function closeModal() {
    show.value = false;
    customer.value = {
        id: "",
        first_name: "",
        last_name: "",
        email: "",
        loading: false,
        billing_address: {
            address1: "",
            address2: "",
            city: "",
            state: "",
            zip_code: "",
            country_code: ""
        },
        shipping_address: {
        address1: "",
        address2: "",
        city: "",
        state: "",
        zip_code: "",
        country_code: ""
    }
    };
    emit("close");
}

const onSubmit = async () => {
    customer.value.loading = true;
    const customerData = { ...customer.value, status: customer.value.status.toString() };
   // console.log('Submitting customer data:', customer.value);
    if (customerData.id) {
        await customerStore
            .updateCustomer(customer.value)
            .then(() => {
                customer.value.loading = false;
                closeModal();
                
            })
            .catch((error) => {
                console.error("Error updating customer:", error);
                customer.value.loading = false;
            });
    }
};

watch(
    () => props.customer,
    (newCustomer) => {
        customer.value = {
            id: newCustomer.id || "",
            first_name: newCustomer.first_name || "",
            last_name: newCustomer.last_name || "",
            email: newCustomer.email || "",
            phone: newCustomer.phone || "",
            status: newCustomer.status || "disabled",
            billing_address: newCustomer.billing_address || {
                address1: "",
                address2: "",
                city: "",
                state: "",
                zip_code: "",
                country_code: ""
            },
            shipping_address: props.customer.shipping_address || {
                address1: "",
                address2: "",
                city: "",
                state: "",
                zip_code: "",
                country_code: ""
            }
        };
    },
    { immediate: true }
);
</script>

<style scoped></style>
