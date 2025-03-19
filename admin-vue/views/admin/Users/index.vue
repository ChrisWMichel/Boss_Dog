<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold">Users</h1>
        <button
            type="button"
            @click="showUsersModal"
            class="px-4 py-2 text-white bg-indigo-500 rounded hover:bg-indigo-700 hover:text-white"
        >
            + New User
        </button>
    </div>
    <modal-create v-model="showModal" :user="usersModel" />
    <users-table @clickEdit="editUser" />
</template>

<script setup>
import { ref, onMounted } from "vue";
import usersTable from "./usersTable.vue";
import modalCreate from "./userAdminModal.vue";

const showModal = ref(false);
const usersModel = ref({
    id: "",
    firstname: "",
    lastname: "",
    email: "",
    is_admin: "",
});

const showUsersModal = () => {
    showModal.value = true;
};

const editUser = (user) => {
    usersModel.value = user;
    showModal.value = true;
};
</script>

<style scoped></style>
