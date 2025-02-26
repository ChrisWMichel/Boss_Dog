<template>
    <div class="flex min-h-screen mx-auto">
        <div
            class="w-[200px] bg-gray-500 text-white fixed top-0 left-0 h-full transition-transform duration-300"
            :class="{ '-translate-x-full': !sidebarOpen }"
        >
            <sidebar />
        </div>

        <div
            class="flex-1 transition-all duration-300"
            :class="{ 'ml-[200px]': sidebarOpen, 'ml-0': !sidebarOpen }"
        >
            <header
                class="fixed top-0 right-0 z-10 h-16 transition-all duration-300 bg-gray-200"
                :class="{ 'left-[200px]': sidebarOpen, 'left-0': !sidebarOpen }"
            >
                <top-menu
                    :sidebarOpen="sidebarOpen"
                    @toggleSidebar="toggleSidebar"
                />
            </header>
            <main class="h-full pt-10 mt-7">
                <div class="h-full p-4 bg-white rounded">
                    <router-view />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import Sidebar from "../Sidebar.vue";
import TopMenu from "../TopMenu.vue";
import { ref, onMounted, onUnmounted } from "vue";

const sidebarOpen = ref(true);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

onMounted(() => {
    handleSidebarOpen();
    window.addEventListener("resize", handleSidebarOpen);
});

onUnmounted(() => {
    window.removeEventListener("resize", handleSidebarOpen);
});

const handleSidebarOpen = () => {
    if (window.outerWidth <= 768) {
        sidebarOpen.value = false;
    } else {
        sidebarOpen.value = true;
    }
};
</script>

<style scoped></style>
