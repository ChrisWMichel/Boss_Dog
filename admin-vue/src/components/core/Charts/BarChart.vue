<template>
  <div class="bar-container">
    <Bar :data="data" :options="options" />
  </div>
</template>

<script setup>
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from "chart.js";
import { Bar } from "vue-chartjs";
//import { defineProps } from "vue";

defineProps({
  data: Object,
});

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const options = {
  responsive: true,
  maintainAspectRatio: false,
};
</script>

<style scoped>
.bar-container {
  width:1250px;
  height: 650px;
}
</style>
