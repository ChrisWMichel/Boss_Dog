<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-10">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div
                    class="flex items-center justify-center min-h-full p-4 text-center"
                >
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl"
                        >
                            <DialogTitle
                                as="h3"
                                class="text-lg font-medium leading-6 text-gray-900"
                            >
                                {{
                                    user.id
                                        ? `Update User: "${user.id}"`
                                        : "Create New User"
                                }}
                            </DialogTitle>
                            <spinner
                                v-if="user.loading"
                                class="flex items-center justify-center"
                            />

                            <form v-else @submit.prevent="onSubmit">
                                <div class="mt-2">
                                    <custom-input
                                        v-model="user.firstname"
                                        label="First Name"
                                        type="text"
                                        id="firstname"
                                        class="mb-2"
                                    />
                                    <custom-input
                                        type="text"
                                        class="mb-2"
                                        label="Last Name"
                                        id="lastname"
                                        v-model="user.lastname"
                                    />
                                    <custom-input
                                        type="text"
                                        class="mb-2"
                                        v-model="user.email"
                                        label="Email"
                                    />
                                    <div v-if="!user.id" class="relative mb-2">
                                        <custom-input
                                            :type="
                                                showPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            class="mb-0"
                                            v-model="user.password"
                                            label="Password"
                                        />
                                        <button
                                            type="button"
                                            @click="
                                                showPassword = !showPassword
                                            "
                                            class="absolute text-gray-500 right-2 top-8 hover:text-gray-700"
                                        >
                                            {{ showPassword ? "Hide" : "Show" }}
                                        </button>
                                    </div>

                                    <!-- For existing users: show password change option -->
                                    <div v-else>
                                        <div class="mb-2">
                                            <label
                                                class="inline-flex items-center"
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="isChangingPassword"
                                                    class="w-4 h-4 text-indigo-600 form-checkbox"
                                                />
                                                <span class="ml-2"
                                                    >Change password</span
                                                >
                                            </label>
                                        </div>
                                        <div
                                            v-if="isChangingPassword"
                                            class="relative mb-2"
                                        >
                                            <custom-input
                                                :type="
                                                    showPassword
                                                        ? 'text'
                                                        : 'password'
                                                "
                                                class="mb-0"
                                                v-model="user.password"
                                                label="New Password"
                                            />
                                            <button
                                                type="button"
                                                @click="
                                                    showPassword = !showPassword
                                                "
                                                class="absolute text-gray-500 right-2 top-8 hover:text-gray-700"
                                            >
                                                {{
                                                    showPassword
                                                        ? "Hide"
                                                        : "Show"
                                                }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex flex-row">
                                        <custom-input
                                            v-model="user.is_admin"
                                            label="Admin"
                                            type="checkbox"
                                        />
                                        Admin
                                    </div>
                                </div>
                                <footer
                                    class="flex flex-col-reverse px-4 py-3 bg-gray-50 sm:px-6 sm:flex-row-reverse"
                                >
                                    <button
                                        :disabled="user.loading"
                                        type="submit"
                                        class="px-4 py-2 mt-2 text-white bg-indigo-500 rounded hover:bg-indigo-700 hover:text-white sm:ml-2 sm:mt-0"
                                    >
                                        Submit
                                    </button>
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="px-4 py-2 text-white bg-gray-500 rounded hover:bg-gray-700 hover:text-white"
                                    >
                                        Cancel
                                    </button>
                                </footer>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from "@headlessui/vue";
import { useUserStore } from "../../../store/useUserStore";
import customInput from "../../../src/components/core/customInput.vue";
import spinner from "../../../src/components/core/spinner.vue";

const userStore = useUserStore();
const isChangingPassword = ref(false);
const showPassword = ref(false);

const props = defineProps({
    modelValue: Boolean,
    user: {
        type: Object,
        default: () => ({}),
    },
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const user = ref({
    id: props.user.id || "",
    firstname: props.user.firstname || "",
    lastname: props.user.lastname || "",
    email: props.user.email || "",
    is_admin: props.user.is_admin || null,
    password: "",
});

const emit = defineEmits(["update:modelValue", "close"]);

function closeModal() {
    show.value = false;
    isChangingPassword.value = false;
    showPassword.value = false;
    user.value = {
        id: "",
        firstname: "",
        lastname: "",
        email: "",
        is_admin: null,
        password: "",
        loading: false,
    };
    emit("close");
}

const onSubmit = async () => {
    user.value.loading = true;
    const userData = { ...user.value };
    if (userData.id && !isChangingPassword.value) {
        delete userData.password;
    }
    if (userData.id) {
        await userStore
            .updateUser(user.value)
            .then(() => {
                user.value.loading = false;
                closeModal();
                user.value = {
                    id: "",
                    firstname: "",
                    lastname: "",
                    email: "",
                    is_admin: null,
                    password: "",
                };
            })
            .catch((error) => {
                console.error("Error updating user:", error);
                user.value.loading = false;
            });
    } else {
        try {
            await userStore.createUser(user.value).then(() => {
                user.value.loading = false;
                closeModal();
                user.value = {
                    id: "",
                    firstname: "",
                    lastname: "",
                    email: "",
                    is_admin: null,
                    password: "",
                };
            });
        } catch (error) {
            console.error("Error submitting form:", error);
            user.value.loading = false;
        }
    }
};

watch(
    () => props.user,
    (newUser) => {
        user.value = {
            id: newUser.id || "",
            firstname: newUser.firstname || "",
            lastname: newUser.lastname || "",
            email: newUser.email || "",
            is_admin: newUser.is_admin || null,
            password: "",
        };
    },
    { immediate: true }
);
</script>

<style scoped></style>
