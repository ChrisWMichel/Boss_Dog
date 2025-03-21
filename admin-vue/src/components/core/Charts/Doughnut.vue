<template>
    <div class="chart-container">
        <Doughnut :data="data" :options="options" />
    </div>
</template>

<script setup>
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
    import { Doughnut } from 'vue-chartjs'
    ChartJS.register(ArcElement, Tooltip, Legend)

    defineProps({
        data: Object
    })
    
    const options = {
        responsive: true,
        maintainAspectRatio: false
    }

    
</script>

<style  scoped>
.chart-container {
    width: 250px;
    height: 350px;
}
</style>