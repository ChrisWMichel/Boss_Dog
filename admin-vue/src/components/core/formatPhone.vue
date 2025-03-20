<template>
    <span>{{ formattedPhone }}</span>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    phone: {
        type: String,
        required: false,
        default: "",
    },
    format: {
        type: String,
        default: "US", // Default format is US
    },
});

const formattedPhone = computed(() => {
    if (!props.phone) return "";

    // Remove all non-digit characters
    const cleaned = props.phone.replace(/\D/g, "");

    // Format based on country/format type
    if (props.format === "US") {
        // US format: (XXX) XXX-XXXX
        if (cleaned.length === 10) {
            return `(${cleaned.slice(0, 3)}) ${cleaned.slice(
                3,
                6
            )}-${cleaned.slice(6, 10)}`;
        } else if (cleaned.length === 11 && cleaned[0] === "1") {
            // Handle with country code: 1-XXX-XXX-XXXX
            return `1-${cleaned.slice(1, 4)}-${cleaned.slice(
                4,
                7
            )}-${cleaned.slice(7, 11)}`;
        }
    } else if (props.format === "international") {
        // Basic international format with country code: +X XXX XXX XXXX
        if (cleaned.length >= 10) {
            // This is a simplified version - real international formatting is more complex
            return `+${cleaned.slice(0, cleaned.length - 10)} ${cleaned.slice(
                -10,
                -7
            )} ${cleaned.slice(-7, -4)} ${cleaned.slice(-4)}`;
        }
    }

    // If no formatting applied or format not recognized, return with basic formatting
    if (cleaned.length > 6) {
        return `${cleaned.slice(0, 3)}-${cleaned.slice(3, 6)}-${cleaned.slice(
            6
        )}`;
    }

    // Return cleaned number if it doesn't match any format
    return cleaned;
});
</script>

<style lang="scss" scoped></style>
