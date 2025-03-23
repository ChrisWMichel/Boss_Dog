<template>
  <div class="flex justify-between">
    <h1 class="text-3xl font-semibold mb-5">Dashboard</h1>
    <select @change="onDatePickerChange" class="px-3 py-2 border rounded-md mb-3 w-64">
      <option value="all">All Time</option>
      <option value="last-day">Last Day</option>
      <option value="last-week">Last Week</option>
      <option value="last-2-week">Last 2 Week</option>
      <option value="last-month">Last Month</option>
      <option value="last-3-month">Last 3 Month</option>
      <option value="last-6-month">Last 6 Month</option>
    </select>
  </div>
  <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
    <div class="box-cards">
      <span class="mr-3 text-lg font-bold">Active Customers</span>
      <template v-if="!loading.activeCustomers">
        {{ activeCustomers }}
      </template>
      <spinner v-else class="my-auto" />
    </div>
    <div class="box-cards" style="animation-delay: 0.1s">
      <span class="mr-3 text-lg font-bold">Active Products</span>
      <template v-if="!loading.activeProducts">
        {{ activeProducts }}
      </template>
      <spinner v-else class="my-auto" />
    </div>
    <div class="box-cards" style="animation-delay: 0.2s">
      <span class="mr-3 text-lg font-bold">Paid Orders</span>
      <template v-if="!loading.paidOrders">
        {{ paidOrders }}
      </template>
      <spinner v-else class="my-auto" />
    </div>
    <div class="box-cards" style="animation-delay: 0.3s">
      <span class="mr-3 text-lg font-bold">Total Sales</span>
      <template v-if="!loading.totalSales">
        <formatPrice :price="totalSales" class="font-semibold text-green-700" />
      </template>
      <spinner v-else class="my-auto" />
    </div>
  </div>

  <div
    class="grid grid-cols-1 gap-3 mt-10 md:grid-flow-col md:grid-cols-3 md:grid-rows-2"
  >
    <div class="md:col-span-2 md:row-span-2 box-card2" style="animation-delay: 0.4s">
      <span class="block mb-4 mr-3 text-lg font-bold text-center">Latest Orders</span>
      <template v-if="!loading.latestOrders">
        <div class="grid max-w-4xl grid-cols-1 gap-4 mx-auto md:grid-cols-2">
          <router-link
            v-for="order in latestOrders"
            :key="order.id"
            :to="{ name: 'app.order.view', params: { id: order.id } }"
            class="flex items-center gap-3 px-3 py-2 text-lg rounded-md text-black/80 hover:bg-gray-300 bg-slate-300"
          >
            <div class="w-full">
              <p>
                <span class="font-semibold text-indigo-700">Order #{{ order.id }}</span> -
                contains {{ order.number_of_items }} Items -
                <formatPrice :price="order.total" class="font-semibold text-green-700" />
              </p>
              <p class="flex justify-between w-3/4">
                <span>{{ order.first_name + " " + order.last_name }}</span>
                <span>{{ order.created_at }}</span>
              </p>
            </div>
          </router-link>
        </div>
      </template>
      <spinner v-else text="Loading Orders..." size="w-32 h-32" class="my-auto" />
    </div>
    <div class="box-cards" style="animation-delay: 0.4s">
      <span class="mr-3 text-lg font-bold">Sales by State</span>
      <template v-if="!loading.ordersByState">
        <Doughnut :data="stateData" />
      </template>
      <spinner v-else class="my-auto mt-5 text-sm" text="Loading Sales by State..." />
    </div>

    <div class="box-cards" style="animation-delay: 0.5s">
      <span class="mb-3 mr-3 text-lg font-bold">Latest Customers</span>
      <template v-if="!loading.latestCustomers">
        <router-link
          :to="{ name: 'app.customers.view', params: { id: customer.id } }"
          v-for="customer in latestCustomers.names"
          :key="customer.id"
          class="flex items-center gap-3 mb-3 rounded-md text-black/80 hover:bg-slate-100"
        >
          <div class="rounded-full bg-slate-200">
            <UserCircleIcon class="w-10 h-10" />
          </div>

          <div class="pr-2">
            <div class="text-lg">{{ customer.name }}</div>
            <p class="text-sm">{{ customer.email }}</p>
          </div>
        </router-link>
      </template>
      <spinner v-else class="my-auto mt-5 text-sm" text="Loading Latest Customers..." />
    </div>
  </div>
</template>

<script setup>
import Doughnut from "../../src/components/core/Charts/Doughnut.vue";
import axiosClient from "../../src/axiosHelper";
import formatPrice from "../../src/components/core/formatPrice.vue";
import spinner from "../../src/components/core/spinner.vue";
import { ref, onMounted } from "vue";
import { UserCircleIcon } from "@heroicons/vue/24/solid";

const loading = ref({
  activeCustomers: true,
  activeProducts: true,
  paidOrders: true,
  totalSales: true,
  ordersByState: true,
  latestCustomers: true,
  latestOrders: true,
});

const activeCustomers = ref(0);
const activeProducts = ref(0);
const paidOrders = ref(0);
const totalSales = ref(0);
const stateData = ref({
  labels: [],
  datasets: [
    {
      backgroundColor: [],
      data: [],
    },
  ],
});
const latestCustomers = ref([]);
const latestOrders = ref([]);

onMounted(() => {
  fetchOrdersByState();
  fetchLatestCustomers();
  fetchLatestOrders();
});

// Update fetch functions to accept period parameter
const fetchOrdersByState = async (period = null) => {
  try {
    const url = period
      ? `/dashboard/orders-by-state?period=${period}`
      : "/dashboard/orders-by-state";
    const response = await axiosClient.get(url);
    const data = response.data;

    stateData.value = {
      labels: data.map((item) => item.state),
      datasets: [
        {
          backgroundColor: generateColors(data.length),
          data: data.map((item) => item.count),
        },
      ],
    };
  } catch (error) {
    console.error("Error fetching orders by state:", error);
  } finally {
    loading.value.ordersByState = false;
  }
};

const fetchLatestCustomers = async (period = null) => {
  try {
    const url = period
      ? `/dashboard/latest-customers?period=${period}`
      : "/dashboard/latest-customers";
    const response = await axiosClient.get(url);
    const data = response.data;

    latestCustomers.value = {
      names: data.map((item) => ({
        name: `${item.first_name} ${item.last_name}`,
        id: item.user.id,
        email: item.user.email,
      })),
    };
  } catch (error) {
    console.error("Error fetching latest customers:", error);
  } finally {
    loading.value.latestCustomers = false;
  }
};

// Fetch data for latest orders
const fetchLatestOrders = async (period = null) => {
  try {
    const url = period
      ? `/dashboard/latest-orders?period=${period}`
      : "/dashboard/latest-orders";
    const response = await axiosClient.get(url);
    const data = response.data;

    latestOrders.value = data;
  } catch (error) {
    console.error("Error fetching latest orders:", error);
  } finally {
    loading.value.latestOrders = false;
  }
};

// Generate random colors for chart
const generateColors = (count) => {
  const colors = [];
  for (let i = 0; i < count; i++) {
    colors.push(`hsl(${Math.random() * 360}, 70%, 50%)`);
  }
  return colors;
};

const onDatePickerChange = async (event) => {
  const selectedOption = event.target.value;

  loading.value = {
    activeCustomers: true,
    activeProducts: true,
    paidOrders: true,
    totalSales: true,
    ordersByState: true,
    latestCustomers: true,
    latestOrders: true,
  };

  try {
    // Fetch dashboard metrics with date filter
    const [
      customersResponse,
      productsResponse,
      ordersResponse,
      salesResponse,
    ] = await Promise.all([
      axiosClient.get(`/dashboard/active-customers?period=${selectedOption}`),
      axiosClient.get(`/dashboard/active-products?period=${selectedOption}`),
      axiosClient.get(`/dashboard/paid-orders?period=${selectedOption}`),
      axiosClient.get(`/dashboard/total-sales?period=${selectedOption}`),
    ]);

    // Update dashboard metrics
    activeCustomers.value = customersResponse.data.active_customers;
    activeProducts.value = productsResponse.data;
    paidOrders.value = ordersResponse.data.paid_orders;
    totalSales.value = salesResponse.data.total_sales;

    // Update loading states
    loading.value.activeCustomers = false;
    loading.value.activeProducts = false;
    loading.value.paidOrders = false;
    loading.value.totalSales = false;

    // Fetch and update chart data
    await fetchOrdersByState(selectedOption);
    await fetchLatestCustomers(selectedOption);
    await fetchLatestOrders(selectedOption);
  } catch (error) {
    console.error("Error fetching filtered dashboard data:", error);
  }
};

axiosClient.get("/dashboard/active-customers").then((response) => {
  activeCustomers.value = response.data.active_customers;
  loading.value.activeCustomers = false;
});
axiosClient.get("/dashboard/active-products").then((response) => {
  activeProducts.value = response.data;
  loading.value.activeProducts = false;
});
axiosClient.get("/dashboard/paid-orders").then((response) => {
  paidOrders.value = response.data.paid_orders;
  loading.value.paidOrders = false;
});
axiosClient.get("/dashboard/total-sales").then((response) => {
  totalSales.value = response.data.total_sales;
  loading.value.totalSales = false;
});
</script>

<style scoped>
.box-cards {
  @apply flex flex-col animate-fade-in-down items-center px-5 py-3 text-3xl translate-x-1 translate-y-1 rounded-lg shadow-lg bg-gray-200 shadow-gray-500/50;
}
.box-card2 {
  @apply py-6 px-5 animate-fade-in-down translate-x-1 translate-y-1 rounded-lg shadow-lg bg-gray-200 shadow-gray-500/50;
}
</style>
