<template>
    <div>
        <Menu as="div" class="relative inline-block text-left">
            <div>
                <MenuButton
                    class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-black rounded-md hover:bg-black/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-black/75"
                >
                    <span class="mt-3 mr-2">{{ user.firstname }} {{ user.lastname }}</span>
                    <UserIcon class="w-5 h-5 mt-3 text-slate-350" aria-hidden="true" />
                </MenuButton>
            </div>

            <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <MenuItems
                    class="absolute right-0 w-32 py-3 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black/5 focus:outline-none"
                >
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                            <a :href="routes['profile.view']" 
                            class="hover:bg-slate-500 hover:text-slate-100"
                               :class="[
                                   active ? 'bg-slate-400 text-slate-100' : 'text-gray-500',
                                   'group flex w-full items-center rounded-md px-2 py-2 text-sm'
                               ]">
                                <UserIcon
                                    class="w-5 h-5 mr-2 text-slate-350"
                                    aria-hidden="true"
                                />
                                Profile
                            </a>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <button
                                @click="logout"
                                :class="[
                                    active
                                        ? 'bg-slate-500 text-slate-100'
                                        : 'text-gray-900',
                                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                ]"
                            >
                                <ArrowLeftStartOnRectangleIcon
                                    class="w-5 h-5 mr-2 text-slate-350"
                                    aria-hidden="true"
                                />
                                Logout
                            </button>
                        </MenuItem>
                    </div>
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>

<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import {
    ArrowLeftStartOnRectangleIcon,
    UserIcon,
} from "@heroicons/vue/24/solid";
import { useUserStore } from "../../../store/useUserStore";
import { useRouter } from "vue-router";
import { ref } from 'vue';

const userStore = useUserStore();
const router = useRouter();
const user = userStore.getUser;

const logout = () => {
    userStore.logout().then(() => {
        router.push({ name: "login" });
    });
};

const routes = ref(window.laravelRoutes);
</script>
