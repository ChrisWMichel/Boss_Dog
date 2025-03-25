<template>
  <div>
    <div class="grid grid-cols-2 gap-3">
        <router-link :to="{ name: 'report.orders' }"
         class="px-3 py-2 text-center text-gray-600 rounded-md bg-slate-200 hover:bg-slate-400 hover:text-gray-200"
         active-class="text-indigo-600 bg-slate-300"
         > Orders Report </router-link>

        <router-link :to="{ name: 'report.customers' }" class="px-3 py-2 text-center text-gray-600 rounded-md bg-slate-200 hover:bg-slate-400 hover:text-gray-200" active-class="text-indigo-600 bg-slate-300"> Customers Report </router-link>
    </div>
  </div>
  <div class="mt-10 box-cards">
    <router-view></router-view>
  </div>
</template>

<script setup></script>

<style scoped>
.box-cards {
  @apply flex flex-col animate-fade-in-down items-center px-5 py-3 text-3xl translate-x-1 translate-y-1 rounded-lg shadow-lg bg-gray-100 shadow-gray-500/50;
}
</style>
