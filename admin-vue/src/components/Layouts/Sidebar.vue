<template>
    <div class="flex flex-col w-48 h-full px-2 py-4 rounded-l-xl">
        <router-link
            :to="{ name: 'app.dashboard' }"
            class="w-full no-underline transition-colors rounded text-slate-200 hover:bg-white/65"
        >
            <div class="flex w-full gap-2 py-3 ml-5 justify-left">
                <span class="w-6 h-6"><HomeIcon /></span>
                <span>Dashboard</span>
            </div>
        </router-link>
        <router-link
            :to="{ name: 'app.products' }"
            class="w-full no-underline transition-colors rounded text-slate-200 hover:bg-white/65 hover:text-white/20"
        >
            <div class="flex w-full gap-2 py-3 ml-5 justify-left">
                <span class="w-6 h-6"><ListBulletIcon /></span>
                <span>Products</span>
            </div>
        </router-link>
        <router-link
            :to="{ name: 'app.orders' }"
            class="w-full no-underline transition-colors rounded text-slate-200 hover:bg-white/65"
        >
            <div class="flex w-full gap-2 py-3 ml-5 justify-left">
                <span class="w-6 h-6"><ShoppingBagIcon /></span>
                <span>Orders</span>
            </div>
        </router-link>
        <router-link
            :to="{ name: 'app.users' }"
            class="w-full no-underline transition-colors rounded text-slate-200 hover:bg-white/65"
        >
            <div class="flex w-full gap-2 py-3 ml-5 justify-left">
                <span class="w-6 h-6"><UserGroupIcon /></span>
                <span>Users</span>
            </div>
        </router-link>
        <router-link
            :to="{ name: 'app.dashboard' }"
            class="w-full no-underline transition-colors rounded text-slate-200 hover:bg-white/65"
        >
            <div class="flex w-full gap-2 py-3 ml-5 justify-left">
                <span class="w-6 h-6"><ChartBarIcon /></span>
                <span>Reports</span>
            </div>
        </router-link>
    </div>
</template>

<script setup>
import { reactive } from "vue";
import {
    HomeIcon,
    UserIcon,
    ListBulletIcon,
    ChartBarIcon,
    ShoppingBagIcon,
    UserGroupIcon,
} from "@heroicons/vue/24/solid";
//
</script>

<style scoped>
ul {
    list-style-type: none;
    padding: 0;
}

li {
    padding: 10px;
}
a:hover {
    color: #666;
}
</style>
