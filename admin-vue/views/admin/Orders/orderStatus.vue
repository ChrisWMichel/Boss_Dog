<template>
    <span
        class="px-2 py-1 text-2xl font-bold text-white rounded"
        :class="{
            'bg-green-500': status === 'paid',
            'bg-yellow-500': status === 'unpaid',
            'bg-red-500': status === 'cancelled',
            'bg-gray-500': status === 'shipped',
            'bg-blue-500': status === 'completed',
        }"
        >{{ status }}</span
    >
</template>

<script setup>
defineProps({
    status: String,
});
</script>

<style lang="scss" scoped></style>
