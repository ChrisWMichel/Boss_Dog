<template>
    <div class="flex items-center justify-between h-16 px-5 shadow-slate-950">
        <button @click="toggleSidebar">
            <Bars3Icon class="w-8 h-8 hover:text-slate-400" />
        </button>
        <div class="flex items-center justify-center flex-1">
            <router-link :to="{ name: 'home' }" class="block mx-auto">
                <img
                    class="mx-auto rounded-md w-14 h-14 custom-img-size"
                    src="../../assets/bossDogLogo.png"
                    alt="Boss Man Company"
                />
            </router-link>
        </div>
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
