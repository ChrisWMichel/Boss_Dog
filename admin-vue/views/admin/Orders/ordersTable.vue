<template>
    <div class="p-4 bg-white rounded-lg shadow animate-fade-in-down">
        <div class="flex justify-between pb-3 border-b-2">
            <div class="flex items-center">
                <span class="mr-3 whitespace-nowrap">Per Page</span>
                <select
                    @change="getOrders(null)"
                    v-model="perPage"
                    class="relative block w-24 px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="ml-3" v-if="orders.data && orders.data.meta">
                    Found {{ orders.data.meta.total }} orders
                </span>
                <span class="ml-3" v-else> Loading orders... </span>
            </div>
            <div>
                <input
                    v-model="search"
                    @change="getOrders(null)"
                    class="relative block w-48 px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                    placeholder="Search by ID"
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
                        @click="sortOrders('id')"
                        title="ID"
                    >
                    </tableSorting>
                    <tableSorting
                        field="customer"
                        :sort-field="sortField"
                        :sort-direction="sortDirection"
                        @click="sortOrders('customer')"
                        title="Customer"
                    >
                    </tableSorting>
                    <tableSorting
                        field="status"
                        title="Status"
                        :sort-field="sortField"
                        :sort-direction="sortDirection"
                        @click="sortOrders('status')"
                    >
                    </tableSorting>
                    <tableSorting
                        field="created_at"
                        :sort-field="sortField"
                        :sort-direction="sortDirection"
                        @click="sortOrders('created_at')"
                        title="Date"
                    >
                    </tableSorting>
                    <tableSorting
                        field="total"
                        :sort-field="sortField"
                        :sort-direction="sortDirection"
                        @click="sortOrders('total')"
                        title="Total"
                    >
                    </tableSorting>
                    <tableSorting
                        field="items"
                        :sort-field="sortField"
                        :sort-direction="sortDirection"
                        @click="sortOrders('items')"
                        title="Items"
                    >
                    </tableSorting>

                    <tableSorting
                        field="actions"
                        class="hover:bg-transparent no-pointer"
                    >
                        Actions
                    </tableSorting>
                </tr>
            </thead>
            <tbody v-if="orders.loading || !orders.data.data.length">
                <tr>
                    <td colspan="6">
                        <spinnerAppLayout v-if="orders.loading" />
                        <p v-else class="py-8 text-center text-gray-700">
                            There are no orders
                        </p>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr
                    v-for="(order, index) of orders.data.data"
                    :key="order.id"
                    class="even:bg-gray-100"
                    :style="{ 'animation-delay': index * 0.1 + 's' }"
                >
                    <td class="p-2 border-b">{{ order.id }}</td>
                    <td class="p-2 border-b">
                        {{ order.user.firstname }} {{ order.user.lastname }}
                    </td>
                    <td class="p-2 border-b">
                        <span
                            class="px-2 py-1 text-white rounded"
                            :class="{
                                'bg-green-500': order.status === 'paid',
                                'bg-yellow-500': order.status === 'unpaid',
                                'bg-red-500': order.status === 'cancelled',
                                'bg-gray-500': order.status === 'shipped',
                                'bg-blue-500': order.status === 'completed',
                            }"
                            >{{ order.status }}</span
                        >
                    </td>
                    <td
                        class="border-b p-2 max-w-[200px] whitespace-nowrap overflow-hidden text-ellipsis"
                    >
                        {{ order.created_at }}
                    </td>
                    <td class="p-2 border-b">${{ order.total }}</td>
                    <td class="p-2 border-b">{{ order.number_of_items }}</td>

                    <td class="relative p-2 border-b">
                        <Menu as="div" class="relative inline-block text-left">
                            <div class="flex items-center space-x-2">
                                <RouterLink
                                    :to="{
                                        name: 'app.order.view',
                                        params: { id: order.id },
                                    }"
                                    class="flex items-center justify-center w-8 h-8 transition-colors duration-200 border border-indigo-600 rounded-full hover:bg-indigo-600 group"
                                >
                                    <EyeIcon
                                        class="w-5 h-5 text-indigo-600 transition-colors duration-200 group-hover:text-white"
                                    />
                                </RouterLink>
                            </div>
                        </Menu>
                    </td>
                </tr>
            </tbody>
        </table>

        <div
            v-if="!orders.loading"
            class="flex items-center justify-center gap-10 mt-5"
        >
            <div v-if="orders.data.data && orders.data.data.length">
                Showing from {{ orders.data.meta.from }} to
                {{ orders.data.meta.to }}
            </div>
            <nav
                class="relative z-0 inline-flex justify-center -space-x-px rounded-md shadow-sm"
                aria-label="Pagination"
            >
                <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
                <a
                    v-for="(link, i) of orders.data.meta.links"
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
                        i === orders.data.links.length - 1
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
import { computed, onMounted, ref, onBeforeMount } from "vue";
import { useOrderStore } from "../../../store/useOrderStore";
import spinnerAppLayout from "../../../src/components/core/spinnerAppLayout.vue";
import tableSorting from "../../../src/components/core/Table/tableSorting.vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import orderStatus from "./orderStatus.vue";
import {
    PencilIcon,
    TrashIcon,
    EllipsisVerticalIcon,
    EyeIcon,
} from "@heroicons/vue/24/solid";

const orderStore = useOrderStore();
const perPage = ref(10);
const search = ref("");
const orders = computed(() => orderStore.orders);
const sortField = ref("updated_at");
const sortDirection = ref("asc");

//console.log("orders:", orders);
//console.log("orders:", orders.value.ordersData.data[0].user);
//console.log("orders:", orders.value.ordersData);

const showOrderModal = ref(false);

const emit = defineEmits(["clickEdit"]);

function getForPage(ev, link) {
    ev.preventDefault();
    if (!link.url || link.active) {
        return;
    }

    getOrders(link.url);
}

const getOrders = async (url = null) => {
    await orderStore
        .getOrders({
            url,
            search: search.value,
            per_page: perPage.value,
            sortField: sortField.value,
            sortDirection: sortDirection.value,
        })
        .catch((error) => {
            console.error("Error fetching orders:", error);
        });
};

onBeforeMount(async () => {
    await getOrders();
});

function sortOrders(field) {
    //console.log("Sorting by field:", field);
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

    getOrders();
}

function showAddNewModal() {
    showOrderModal.value = true;
}

function deleteOrder(order) {
    if (!confirm(`Are you sure you want to delete the order?`)) {
        return;
    }

    orderStore
        .deleteOrder(order.id)
        .then(() => {
            getOrders();
        })
        .catch((error) => {
            console.error("Error deleting order:", error);
        });
}

function showOrder(p) {
    emit("clickShow", p);
}
</script>

<style scoped>
.no-pointer {
    cursor: default;
}
.custom-dropdown {
    z-index: 150;
}
.relative {
    position: relative;
}
</style>
