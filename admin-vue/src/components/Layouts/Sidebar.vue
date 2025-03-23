<template>
  <div class="flex flex-col w-48 h-full px-2 py-4 rounded-l-xl">
    <router-link
      v-for="(item, index) in menuItems"
      :key="index"
      :to="item.to"
      :class="[
        'w-full no-underline transition-colors rounded text-slate-200 hover:bg-white/65',
        $route.name === item.name ? 'bg-white/25' : '',
      ]"
      @click.native="handleClick"
    >
      <div class="flex w-full gap-2 py-3 ml-5 justify-left">
        <span class="w-6 h-6"><component :is="item.icon" /></span>
        <span>{{ item.text }}</span>
      </div>
    </router-link>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import {
  HomeIcon,
  UserIcon,
  ListBulletIcon,
  ChartBarIcon,
  ShoppingBagIcon,
  UserGroupIcon,
  UsersIcon,
} from "@heroicons/vue/24/solid";

const $route = useRoute();

// Define menu items as an array for cleaner template
const menuItems = reactive([
  {
    text: "Dashboard",
    to: { name: "app.dashboard" },
    icon: HomeIcon,
    name: "app.dashboard",
  },
  {
    text: "Products",
    to: { name: "app.products" },
    icon: ListBulletIcon,
    name: "app.products",
  },
  {
    text: "Orders",
    to: { name: "app.orders" },
    icon: ShoppingBagIcon,
    name: "app.orders",
  },
  { text: "Users", to: { name: "app.users" }, icon: UserGroupIcon, name: "app.users" },
  {
    text: "Customers",
    to: { name: "app.customers" },
    icon: UsersIcon,
    name: "app.customers",
  },
  {
    text: "Reports",
    to: { name: "app.dashboard" },
    icon: ChartBarIcon,
    name: "app.dashboard",
  },
]);

// Handle click to force router update
const handleClick = () => {
  // Force a small delay to ensure proper navigation
  setTimeout(() => {
    window.dispatchEvent(new Event("popstate"));
  }, 50);
};

// Clean up on component unmount
onUnmounted(() => {
  // Clean up any potential event listeners
  const elements = document.querySelectorAll(".router-link-active");
  elements.forEach((el) => {
    el.classList.remove("router-link-active");
  });
});
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
