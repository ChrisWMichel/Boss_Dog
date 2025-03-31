<template>
  <div class="bar-container">
    <Line :data="data" :options="options" />
  </div>
</template>

<script setup>
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  PointElement,
  LinearScale,
} from "chart.js";
import { Line } from "vue-chartjs";
//import { defineProps } from "vue";

defineProps({
  data: Object,
});

ChartJS.register(PointElement,CategoryScale, LinearScale, LineElement, Title, Tooltip, Legend);

const options = {
  responsive: true,
  maintainAspectRatio: false,
};
</script>

<style scoped>
.bar-container {
  width: 1250px;
  height: 650px;
}
</style>
