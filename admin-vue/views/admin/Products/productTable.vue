<template>
    <div class="p-4 bg-white rounded-lg shadow animate-fade-in-down">
        <div class="flex justify-between pb-3 border-b-2">
            <div class="flex items-center">
                <span class="mr-3 whitespace-nowrap">Per Page</span>
                <select
                    @change="getProducts(null)"
                    v-model="perPage"
                    class="relative block w-24 px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="ml-3">Found {{ products.total }} products</span>
            </div>
            <div>
                <input
                    v-model="search"
                    @change="getProducts(null)"
                    class="relative block w-48 px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                    placeholder="Type to Search products"
                />
            </div>
        </div>

        <table class="w-full table-auto">
            <thead>
                <tr>
                    <tableSorting
                        field="id"
                        :sort-field="sortField"
                        :sort-direction="sortDirection"
                        @click="sortProducts('id')"
                        title="ID"
                    >
                    </tableSorting>
                    <tableSorting
                        field="image"
                        title="Image"
                        class="cursor-auto hover:bg-transparent"
                    >
                    </tableSorting>
                    <tableSorting
                        field="title"
                        :sort-field="sortField"
                        :sort-direction="sortDirection"
                        @click="sortProducts('title')"
                        title="Title"
                    >
                    </tableSorting>
                    <tableSorting
                        field="price"
                        :sort-field="sortField"
                        :sort-direction="sortDirection"
                        @click="sortProducts('price')"
                        title="Price"
                    >
                    </tableSorting>
                    <tableSorting
                        field="updated_at"
                        :sort-field="sortField"
                        :sort-direction="sortDirection"
                        @click="sortProducts('updated_at')"
                        title="Updated At"
                    >
                    </tableSorting>
                    <tableSorting field="actions"> Actions </tableSorting>
                </tr>
            </thead>
            <tbody v-if="products.loading || !products.data.data.length">
                <tr>
                    <td colspan="6">
                        <spinnerAppLayout v-if="products.loading" />
                        <p v-else class="py-8 text-center text-gray-700">
                            There are no products
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr
                    v-for="(product, index) of products.data.data"
                    :key="product.id"
                    class="animate-fade-in-down even:bg-gray-100"
                    :style="{ 'animation-delay': index * 0.1 + 's' }"
                >
                    <td class="p-2 border-b">{{ product.id }}</td>
                    <td class="p-2 border-b">
                        <img
                            class="object-cover w-16 h-16"
                            :src="product.image"
                            :alt="product.title"
                        />
                    </td>
                    <td
                        class="border-b p-2 max-w-[200px] whitespace-nowrap overflow-hidden text-ellipsis"
                    >
                        {{ product.title }}
                    </td>
                    <td class="p-2 border-b">${{ product.price }}</td>
                    <td class="p-2 border-b">
                        {{ product.updated_at }}
                    </td>
                    <td class="p-2 border-b">
                        <Menu as="div" class="relative inline-block text-left">
                            <div>
                                <MenuButton
                                    class="inline-flex items-center justify-center w-full h-10 text-sm font-medium text-white bg-black bg-opacity-0 rounded-full hover:bg-opacity-5 focus:bg-opacity-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
                                >
                                    <EllipsisVerticalIcon
                                        class="w-5 h-5 text-indigo-500"
                                        aria-hidden="true"
                                    />
                                </MenuButton>
                            </div>

                            <transition
                                enter-active-class="transition duration-100 ease-out"
                                enter-from-class="transform scale-95 opacity-0"
                                enter-to-class="transform scale-100 opacity-100"
                                leave-active-class="transition duration-75 ease-in"
                                leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0"
                            >
                                <MenuItems
                                    class="absolute right-0 z-10 w-32 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                >
                                    <div class="px-1 py-1">
                                        <MenuItem v-slot="{ active }">
                                            <button
                                                :class="[
                                                    active
                                                        ? 'bg-indigo-600 text-white'
                                                        : 'text-gray-900',
                                                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                                ]"
                                                @click="editProduct(product)"
                                            >
                                                <PencilIcon
                                                    :active="active"
                                                    class="w-5 h-5 mr-2 text-indigo-400"
                                                    aria-hidden="true"
                                                />
                                                Edit
                                            </button>
                                        </MenuItem>
                                        <MenuItem v-slot="{ active }">
                                            <button
                                                :class="[
                                                    active
                                                        ? 'bg-indigo-600 text-white'
                                                        : 'text-gray-900',
                                                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                                ]"
                                                @click="deleteProduct(product)"
                                            >
                                                <TrashIcon
                                                    :active="active"
                                                    class="w-5 h-5 mr-2 text-indigo-400"
                                                    aria-hidden="true"
                                                />
                                                Delete
                                            </button>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </td>
                </tr>
            </tbody>
        </table>

        <div
            v-if="!products.loading"
            class="flex items-center justify-center gap-10 mt-5"
        >
            <div v-if="products.data.data.length">
                Showing from {{ products.data.meta.from }} to
                {{ products.data.meta.to }}
            </div>
            <nav
                class="relative z-0 inline-flex justify-center -space-x-px rounded-md shadow-sm"
                aria-label="Pagination"
            >
                <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
                <a
                    v-for="(link, i) of products.data.meta.links"
                    :key="i"
                    :disabled="!link.url"
                    href="#"
                    @click="getForPage($event, link)"
                    aria-current="page"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium border whitespace-nowrap"
                    :class="[
                        link.active
                            ? 'z-10 bg-indigo-50  text-indigo-600'
                            : 'bg-white  text-gray-500 hover:bg-gray-50',
                        i === 0 ? 'rounded-l-md' : '',
                        i === products.data.links.length - 1
                            ? 'rounded-r-md'
                            : '',
                        !link.url ? ' bg-gray-100 text-gray-700' : '',
                    ]"
                    v-html="link.label"
                >
                </a>
            </nav>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useProductStore } from "../../../store/useProductStore";
import spinnerAppLayout from "../../../src/components/core/spinnerAppLayout.vue";
import tableSorting from "../../../src/components/core/Table/tableSorting.vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import {
    PencilIcon,
    TrashIcon,
    EllipsisVerticalIcon,
} from "@heroicons/vue/24/solid";
import ProductModal from "./ProductModal.vue";

const productStore = useProductStore();
const perPage = ref(10);
const search = ref("");
const products = computed(() => productStore.products);
const sortField = ref("updated_at");
const sortDirection = ref("desc");

//console.log("productsTable", products.value);

//const product = ref({});
const showProductModal = ref(false);

const emit = defineEmits(["clickEdit"]);

onMounted(() => {
    getProducts();
});

function getForPage(ev, link) {
    ev.preventDefault();
    if (!link.url || link.active) {
        return;
    }

    getProducts(link.url);
}

function getProducts(url = null) {
    console.log("sortDirection", sortDirection.value);
    console.log("sortField", sortField.value);
    productStore.getProducts({
        url,
        search: search.value,
        per_page: perPage.value,
        sortField: sortField.value,
        sortDirection: sortDirection.value,
    });
}

function sortProducts(field) {
    console.log("Sorting by field:", field);
    if (field === sortField.value) {
        if (sortDirection.value === "desc") {
            sortDirection.value = "asc";
        } else {
            sortDirection.value = "desc";
        }
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }

    getProducts();
}

function showAddNewModal() {
    showProductModal.value = true;
}

function deleteProduct(product) {
    if (!confirm(`Are you sure you want to delete the product?`)) {
        return;
    }

    productStore.deleteProduct(product.id).then(() => {
        getProducts();
    });
}

function editProduct(p) {
    emit("clickEdit", p);
}
</script>

<style scoped></style>
