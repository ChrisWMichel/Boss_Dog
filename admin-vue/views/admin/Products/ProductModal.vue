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
                                {{
                                    product.id
                                        ? `Update Product: "${product.title}"`
                                        : "Create New Product"
                                }}
                            </DialogTitle>
                            <spinner
                                v-if="product.loading"
                                class="flex items-center justify-center"
                            />
                            <form v-else @submit.prevent="onSubmit">
                                <div class="mt-2">
                                    <custom-input
                                        v-model="product.title"
                                        label="Title"
                                        type="text"
                                        id="title"
                                        class="mb-2"
                                    />
                                    <custom-input
                                        type="file"
                                        class="mb-2"
                                        label="Image"
                                        v-model="product.image"
                                    />
                                    <custom-input
                                        type="textarea"
                                        class="mb-2"
                                        v-model="product.description"
                                        label="Description"
                                    />
                                    <custom-input
                                        v-model="product.price"
                                        label="Price"
                                        type="number"
                                        class="mb-2"
                                        prepend="$"
                                    />
                                </div>
                                <footer
                                    class="flex flex-col-reverse px-4 py-3 bg-gray-50 sm:px-6 sm:flex-row-reverse"
                                >
                                    <button
                                        :disabled="product.loading"
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
import { useProductStore } from "../../../store/useProductStore";
import customInput from "../../../src/components/core/customInput.vue";
import spinner from "../../../src/components/core/spinner.vue";

const productStore = useProductStore();

const props = defineProps({
    modelValue: Boolean,
    product: {
        type: Object,
        default: () => ({}),
    },
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const product = ref({
    id: props.product.id || "",
    title: props.product.title || "",
    price: props.product.price || "",
    description: props.product.description || "",
    image: props.product.image || null,
});

const emit = defineEmits(["update:modelValue", "close"]);

function closeModal() {
    show.value = false;
    emit("close");
}

// function updateImage(event) {
//     console.log("updateImage:", event.target.files[0]);
//     product.value.image = event.target.files[0];
// }

const onSubmit = async () => {
    try {
        product.value.loading = true;
        await productStore.createProduct(product.value).then(() => {
            product.value.loading = false;
            closeModal();
            product.value = {
                id: "",
                title: "",
                price: "",
                description: "",
                image: null,
            };
        });
    } catch (error) {
        console.error("Error submitting form:", error);
    }
};

watch(
    () => props.product,
    (newProduct) => {
        product.value = {
            id: newProduct.id || "",
            title: newProduct.title || "",
            price: newProduct.price || "",
            description: newProduct.description || "",
            image: newProduct.image || null,
        };
    },
    { immediate: true }
);
</script>

<style scoped></style>
