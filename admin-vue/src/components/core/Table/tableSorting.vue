<template>
    <th
        @click="$emit('click', field)"
        class="p-2 text-left border-b-2 cursor-pointer hover:bg-black/20"
        :class="field === sortField ? 'bg-black/10' : ''"
    >
        <div class="block -mr-20">
            <span inline-block align-middle>
                <slot>{{ title }}</slot>
            </span>

            <span
                v-if="sortField === field"
                class="inline-block ml-2 align-middle"
            >
                <BarsArrowUpIcon
                    v-if="sortDirection === 'asc'"
                    class="w-4 h-4 text-black"
                />
                <BarsArrowDownIcon v-else class="w-4 h-4 text-black" />
            </span>
        </div>
    </th>
</template>

<script setup>
import { BarsArrowDownIcon, BarsArrowUpIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    title: String,
    field: String,
    sortField: String,
    sortDirection: String,
});

const emit = defineEmits(["click"]);
</script>

<style scoped>
.title-width {
    width: -30px;
}
</style>
