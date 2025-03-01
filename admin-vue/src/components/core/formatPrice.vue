<template>
    <span>{{ formattedPrice }}</span>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    price: {
        type: [String, Number],
        required: true,
        default: 0,
    },
});

const formattedPrice = computed(() => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(props.price);
});
</script>

<style></style>
