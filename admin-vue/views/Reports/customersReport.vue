<template>
  <div>Customers Report</div>
  <div class="flex flex-col gap-4 md:flex-row">
    <div class="w-full md:w-[85%]">
      <LineChart v-if="chartData.labels.length > 0" :data="chartData" />
      <spinner
        v-else
        class="my-auto mt-5 text-sm"
        size="w-32 h-32"
        text="Loading Customers..."
      />
    </div>
    <div class="w-full md:w-[15%]">
      <h3 class="mb-2 text-lg font-medium">Date From</h3>
      <VueDatePicker Single-picker="MM/dd/yyyy" v-model="dateFrom" />
      <h3 class="mb-2 text-lg font-medium">Date To</h3>
      <VueDatePicker Single-picker="MM/dd/yyyy" v-model="dateTo" />
      <div class="flex justify-center mt-4">
        <button
          @click="applyDateFilter"
          class="px-4 py-2 text-lg text-white bg-indigo-500 rounded hover:bg-indigo-700 hover:text-white"
        >
          Filter
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import axiosClient from "../../src/axiosHelper";
import LineChart from "../../src/components/core/Charts/LineChart.vue";
import { ref, onMounted } from "vue";
import spinner from "../../src/components/core/spinner.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const dateFrom = ref();
const dateTo = ref();

const chartData = ref({
  labels: [],
  datasets: [
    {
      label: "Customers",
      backgroundColor: "#4bc0c0",
      data: [],
    },
  ],
});

const applyDateFilter = () => {
  if (dateFrom.value && dateTo.value) {
    let startDate, endDate;
    
    // Check if dateFrom is already a string or a Date object
    if (typeof dateFrom.value === 'string') {
      startDate = dateFrom.value;
    } else if (dateFrom.value instanceof Date) {
      startDate = dateFrom.value.toISOString().split('T')[0];
    } else {
      startDate = new Date(dateFrom.value).toISOString().split('T')[0];
    }
    
    if (typeof dateTo.value === 'string') {
      endDate = dateTo.value;
    } else if (dateTo.value instanceof Date) {
      endDate = dateTo.value.toISOString().split('T')[0];
    } else {
      endDate = new Date(dateTo.value).toISOString().split('T')[0];
    }
    
    // Fetch with date range
    axiosClient
      .get("/report/customers", { 
        params: {
          start_date: startDate,
          end_date: endDate
        }
      })
      .then((response) => {
        chartData.value = response.data;
        console.log("Filtered chart data:", chartData.value);
      })
      .catch((error) => {
        console.error("Error fetching customer data:", error);
      });
  } else {
    fetchCustomerData();
  }
};

const fetchCustomerData = () => {
  axiosClient.get("report/customers").then((response) => {
    chartData.value = response.data;
    console.log("Chart data:", chartData.value);
  });
};

onMounted(() => {
  applyDateFilter();
});
</script>

<style scoped></style>
