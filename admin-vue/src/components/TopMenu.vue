<template>
    <div class="flex items-center justify-between h-16 px-5 shadow-slate-950">
        <button @click="toggleSidebar">
            <Bars3Icon class="w-8 h-8 hover:text-slate-400" />
        </button>
        <div><profile-dropdown /></div>
    </div>
</template>

<script setup>
import {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
} from "@headlessui/vue";
import { Bars3Icon, BellIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import ProfileDropdown from "./ProfileDropdown.vue";

const props = defineProps({
    sidebarOpen: Boolean,
});

const emit = defineEmits(["toggleSidebar"]);

const toggleSidebar = () => {
    emit("toggleSidebar");
};
</script>

<style scoped></style>
