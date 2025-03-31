<template>
  <div>Orders Report</div>
  <div class="flex flex-col gap-4 md:flex-row">
    <div class="w-full md:w-[85%]">
      <BarChart v-if="chartData.labels.length > 0" :data="chartData" />
      <spinner
        v-else
        class="my-auto mt-5 text-sm"
        size="w-32 h-32"
        text="Loading Orders..."
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
import BarChart from "../../src/components/core/Charts/BarChart.vue";
import { ref, onMounted } from "vue";
import spinner from "../../src/components/core/spinner.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const dateFrom = ref();
const dateTo = ref();

const rawData = ref([]);
// Initialize empty chart data
const chartData = ref({
  labels: [],
  datasets: [
    {
      label: "Order Total",
      backgroundColor: "#4bc0c0",
      data: [],
    },
  ],
});

const fetchOrderData = () => {
  axiosClient
    .get("/report/orders")
    .then((response) => {
      rawData.value = response.data;
      //console.log("Raw data:", rawData.value);

      // Format data for chart.js
      if (Array.isArray(response.data) && response.data.length > 0) {
        // Group orders by date and sum their totals
        const groupedData = {};

        response.data.forEach((order) => {
          let dateLabel;

          // If created_at contains human-readable format like "1 week ago",
          // just use it directly as a label
          if (
            typeof order.created_at === "string" &&
            (order.created_at.includes("ago") ||
              order.created_at.includes("day") ||
              order.created_at.includes("week") ||
              order.created_at.includes("month"))
          ) {
            dateLabel = order.created_at;
          } else {
            // Otherwise try to parse it as a date
            try {
              const date = new Date(order.created_at);
              if (!isNaN(date.getTime())) {
                dateLabel = date.toLocaleDateString();
              } else {
                console.warn("Invalid date:", order.created_at);
                dateLabel = order.created_at; // Fallback to the original string
              }
            } catch (e) {
              console.error("Error parsing date:", e);
              dateLabel = order.created_at || "Unknown date";
            }
          }

          // Add to grouped data
          if (!groupedData[dateLabel]) {
            groupedData[dateLabel] = 0;
          }
          groupedData[dateLabel] += Number(order.total);
        });

        // Convert grouped data to chart format
        const labels = Object.keys(groupedData);
        const data = Object.values(groupedData);

        chartData.value = {
          labels: labels,
          datasets: [
            {
              label: "Order Total",
              backgroundColor: "#4bc0c0",
              data: data,
            },
          ],
        };
      }
      //console.log("Chart data:", chartData.value);
    })
    .catch((error) => {
      console.error("Error fetching order data:", error);
    });
};

const applyDateFilter = () => {
  if (dateFrom.value && dateTo.value) {
    let startDate, endDate;

    // Check if dateFrom is already a string or a Date object
    if (typeof dateFrom.value === "string") {
      startDate = dateFrom.value;
    } else if (dateFrom.value instanceof Date) {
      startDate = dateFrom.value.toISOString().split("T")[0];
    } else {
      startDate = new Date(dateFrom.value).toISOString().split("T")[0];
    }

    if (typeof dateTo.value === "string") {
      endDate = dateTo.value;
    } else if (dateTo.value instanceof Date) {
      endDate = dateTo.value.toISOString().split("T")[0];
    } else {
      endDate = new Date(dateTo.value).toISOString().split("T")[0];
    }

    // Fetch with date range
    axiosClient
      .get("/report/orders", {
        params: {
          start_date: startDate,
          end_date: endDate,
        },
      })
      .then((response) => {
        rawData.value = response.data;
        console.log("Filtered rawData:", rawData.value);

        // Format data for chart.js
        if (Array.isArray(response.data) && response.data.length > 0) {
          // Group orders by date and sum their totals
          const groupedData = {};

          response.data.forEach((order) => {
            let dateLabel;

            // If created_at contains human-readable format like "1 week ago",
            // just use it directly as a label
            if (
              typeof order.created_at === "string" &&
              (order.created_at.includes("ago") ||
                order.created_at.includes("day") ||
                order.created_at.includes("week") ||
                order.created_at.includes("month"))
            ) {
              dateLabel = order.created_at;
            } else {
              // Otherwise try to parse it as a date
              try {
                const date = new Date(order.created_at);
                if (!isNaN(date.getTime())) {
                  dateLabel = date.toLocaleDateString();
                } else {
                  console.warn("Invalid date:", order.created_at);
                  dateLabel = order.created_at; // Fallback to the original string
                }
              } catch (e) {
                console.error("Error parsing date:", e);
                dateLabel = "Unknown date";
              }
            }

            // Add to grouped data
            if (!groupedData[dateLabel]) {
              groupedData[dateLabel] = 0;
            }
            groupedData[dateLabel] += Number(order.total);
          });

          // Convert grouped data to chart format
          const labels = Object.keys(groupedData);
          const data = Object.values(groupedData);

          chartData.value = {
            labels: labels,
            datasets: [
              {
                label: "Order Total",
                backgroundColor: "#4bc0c0",
                data: data,
              },
            ],
          };
        }
        console.log("Filtered chartData:", chartData.value);
      })
      .catch((error) => {
        console.error("Error fetching order data:", error);
      });
  } else {
    fetchOrderData();
  }
};

onMounted(() => {
  fetchOrderData();
});
</script>

<style scoped></style>
