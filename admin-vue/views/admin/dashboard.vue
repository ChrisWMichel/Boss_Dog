<template>
    <h1 class="text-3xl font-semibold">Dashboard</h1>
    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
        <div class="box-cards">
            <span class="mr-3 text-lg font-bold ">Active Customers</span>
            <template v-if="!loading.activeCustomers">
               
                {{activeCustomers}}
            </template> 
            <spinner v-else class="my-auto"/>  
        </div>
        <div class="box-cards">
            <span class="mr-3 text-lg font-bold">Active Products</span>
            <template v-if="!loading.activeProducts">
                
                {{activeProducts}}
            </template>
            <spinner v-else class="my-auto"/>
        </div>
        <div class="box-cards">
            <span class="mr-3 text-lg font-bold">Paid Orders</span>
            <template v-if="!loading.paidOrders">
                
                {{paidOrders}}
            </template>
            <spinner v-else class="my-auto"/>
        </div>
        <div class="box-cards">
            <span class="mr-3 text-lg font-bold">Total Sales</span>
            <template v-if="!loading.totalSales">
                
                <formatPrice :price="totalSales" class="font-semibold text-green-700"/>
            </template>
            <spinner v-else class="my-auto"/>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 mt-10 md:grid-flow-col md:grid-cols-3 md:grid-rows-2">
        <div class="md:col-span-2 md:row-span-2 box-cards">
           Products
           <!-- <spinner text="Loading Products..." size="w-32 h-32" class="my-auto" /> -->
        </div>
        <div class="box-cards">
            <span class="mr-3 text-lg font-bold">Sales by State</span>
            <template v-if="!loading.ordersByState">
                <SaleByState :data="stateData" />
            </template>
            <spinner v-else class="my-auto mt-5"/>
        </div>
       
        <div class="box-cards">
           Customers
        </div>
        
        
    </div>
</template>

<script setup>
import SaleByState from "../../src/components/core/Charts/Doughnut.vue";
import axiosClient from "../../src/axiosHelper";
import formatPrice from "../../src/components/core/formatPrice.vue";
import spinner from "../../src/components/core/spinner.vue";
import { ref, onMounted } from "vue";

const loading = ref({
    activeCustomers: true,
    activeProducts: true,
    paidOrders: true,
    totalSales: true,
    ordersByState: true,
})

const activeCustomers = ref(0);
const activeProducts = ref(0);
const paidOrders = ref(0);
const totalSales = ref(0);
const stateData = ref({
    labels: [],
    datasets: [{
        backgroundColor: [],
        data: []
    }]
});

onMounted(() => {
    fetchOrdersByState();
});

// Fetch data for orders by state
const fetchOrdersByState = async () => {
    try {
        const response = await axiosClient.get("/dashboard/orders-by-state");
        const data = response.data;
        console.log("Orders by state data:", data);
        stateData.value = {
            labels: data.map(item => item.state),
            datasets: [{
                backgroundColor: generateColors(data.length),
                data: data.map(item => item.count)
            }]
        };
    } catch (error) {
        console.error("Error fetching orders by state:", error);
    } finally {
        loading.value.ordersByState = false;
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

const ordersByState = () => {

}



</script>

<style scoped>
.box-cards{
    @apply flex flex-col items-center px-5 py-3 text-3xl translate-x-1 translate-y-1 rounded-lg shadow-lg bg-slate-200 shadow-gray-500/50
}
.box-card2{
    @apply  py-6 px-5 flex flex-col items-center justify-center translate-x-1 translate-y-1 rounded-lg shadow-lg bg-slate-200 shadow-gray-500/50
}
</style>
