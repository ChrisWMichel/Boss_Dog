<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold">Products</h1>
        <router-link
            to="/admin/products/create"
            class="px-4 py-2 text-white bg-indigo-500 rounded hover:bg-indigo-700 hover:text-white"
        >
            + New Product
        </router-link>
    </div>
    <div class="p-4 rounded-lg shadow-md bg-slate-300">
        <div class="flex justify-between pb-3 border-b-2">
            <div class="flex items-center">
                <span class="mr-3 whitespace-nowrap">Per Page</span>
                <select
                    @change="getProducts(null)"
                    v-model="perPage"
                    class="px-3 py-2 leading-tight text-gray-700 border rounded appearance-none focus:outline-none focus:shadow-outline"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div>
                <input
                    v-model="search"
                    @keyup="getProducts(null)"
                    type="text"
                    class="px-3 py-2 leading-tight text-gray-700 border rounded appearance-none focus:outline-none focus:shadow-outline"
                    placeholder="Search..."
                />
            </div>
        </div>

        <spinner-app-layout v-if="products.loading" />
        <template v-else>
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="p-2 text-left border-b-2">ID</th>
                        <th class="p-2 text-left border-b-2">Image</th>
                        <th class="p-2 text-left border-b-2">Title</th>
                        <th class="p-2 text-left border-b-2">Price</th>
                        <th class="p-2 text-left border-b-2">Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products.data.data" :key="product.id">
                        <td class="p-2 border-b-2">{{ product.id }}</td>
                        <td class="p-2 border-b-2">
                            <img
                                :src="product.image"
                                alt="product.title"
                                class="object-cover w-16 rounded"
                            />
                        </td>
                        <td
                            class="border-b-2 p-2 max-w-[200px] whitespace-nowrap overflow-hidden text-ellipsis"
                        >
                            {{ product.title }}
                        </td>
                        <td class="p-2 border-b-2">
                            <format-price :price="product.price" />
                        </td>
                        <td class="p-2 border-b-2">
                            {{ product.updated_at }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div
                v-if="products.data.data.length > 0"
                class="flex justify-center my-4"
            >
                <span>
                    Showing from {{ products.data.meta.from }} to
                    {{ products.data.meta.to }} of
                    {{ products.data.meta.total }} entries
                </span>
                <nav
                    v-if="products.data.meta.links.length > 0"
                    class="relative z-0 inline-flex justify-center ml-10 -space-x-px rounded-md shadow-sm bg-slate-200"
                    aria-label="Pagination"
                >
                    <a
                        v-for="(link, i) in products.data.meta.links"
                        :key="link.url"
                        :href="link.url"
                        :disabled="!link.url"
                        @click="getForPage($event, link)"
                        :class="
                            ((link.active
                                ? 'bg-indigo-500 text-white'
                                : 'bg-white text-gray-500 hover:bg-gray-100',
                            i === 0 ? 'rounded-l-md' : '',
                            i === products.data.meta.links.length - 1
                                ? 'rounded-r-md'
                                : ''),
                            !link.url
                                ? 'pointer-events-none bg-gray-100 text-gray-700'
                                : '')
                        "
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium border border-gray-300 cursor-pointer"
                        v-html="link.label"
                    ></a>
                </nav>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useProductStore } from "../../../store/useProductStore";
import spinnerAppLayout from "../../../src/components/core/spinnerAppLayout.vue";
import formatPrice from "../../../src/components/core/formatPrice.vue";
//import { PRODUCTS_PER_PAGE } from "../../../src/constants";

const productStore = useProductStore();
const perPage = ref(10);
const search = ref("");
const products = computed(() => productStore.products);

onMounted(() => {
    getProducts(null);
});

const getProducts = (url = null) => {
    productStore.getProducts();
};

const getForPage = (e, link) => {
    if (!link.url || link.active) {
        e.preventDefault();
        return;
    }
    e.preventDefault();
    productStore.getProducts(link.url);
    window.scrollTo({ top: 0, behavior: "smooth" });
};
</script>

<style scoped></style>
