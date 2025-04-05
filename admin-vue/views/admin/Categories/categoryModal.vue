<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <TransitionRoot as="template" :show="show">
    <Dialog as="div" class="relative z-10" @close="show = false">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 transition-opacity bg-black bg-opacity-70" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div
          class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0"
        >
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel
              class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-[700px] sm:w-full"
            >
              <spinner
                v-if="category.loading"
                class="absolute top-0 bottom-0 left-0 right-0 flex items-center justify-center bg-white"
              />
              <header class="flex items-center justify-between px-4 py-3">
                <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                  {{
                    category.id
                      ? `Update category: "${props.category.name}"`
                      : "Create new Category"
                  }}
                </DialogTitle>
                <button
                  @click="closeModal()"
                  class="w-8 h-8 flex items-center justify-center rounded-full transition-colors cursor-pointer hover:bg-[rgba(0,0,0,0.2)]"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-6 h-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </header>
              <form @submit.prevent="onSubmit">
                <div class="px-4 pt-5 pb-4 bg-white">
                  <customInput
                    :errors="errors['name']"
                    class="mb-2"
                    v-model="category.name"
                    label="Name"
                  />
                  <customInput
                    type="select"
                    :select-options="parentCategories"
                    class="mb-2"
                    v-model="category.parent_id"
                    label="Parent"
                    :errors="errors['parent_id']"
                  />
                  <div class="flex">
                    <customInput
                      type="checkbox"
                      class="mb-2"
                      v-model="category.active"
                      label="Active"
                      :errors="errors['active']"
                    />
                    <span>Active</span>
                  </div>
                </div>
                <footer class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                  <button
                    type="submit"
                    class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-white bg-indigo-600 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm hover:bg-indigo-700 focus:ring-indigo-500"
                  >
                    Submit
                  </button>
                  <button
                    type="button"
                    class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    @click="closeModal"
                    ref="cancelButtonRef"
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
import { computed, onUpdated, ref } from "vue";
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
//import { ExclamationIcon } from "@heroicons/vue/24/outline";
import customInput from "../../../src/components/core/customInput.vue";
import { useCategoryStore } from "../../../store/useCategoryStore";
import spinner from "../../../src/components/core/spinner.vue";

const errors = ref({});

const props = defineProps({
  modelValue: Boolean,
  category: {
    required: true,
    type: Object,
  },
});

const categoryStore = useCategoryStore();

const category = ref({
  id: props.category.id,
  name: props.category.name,
  active: props.category.active,
  parent_id: props.category.parent_id,
  loading: false,
});

const emit = defineEmits(["update:modelValue", "close"]);

const show = computed({
  get: () => props.modelValue,
  set: (value) => emit("update:modelValue", value),
});
const parentCategories = computed(() => {
  return [
    { key: "", text: "Select Parent Category" },
    ...categoryStore.categories.data
      .filter((c) => {
        if (category.value.id) {
          return c.id !== category.value.id;
        }
        return true;
      })
      .map((c) => ({ key: c.id, text: c.name }))
      .sort((c1, c2) => {
        if (c1.text < c2.text) return -1;
        if (c1.text > c2.text) return 1;
        return 0;
      }),
  ];
});

onUpdated(() => {
  category.value = {
    id: props.category.id,
    name: props.category.name,
    active: props.category.active,
    parent_id: props.category.parent_id,
  };
});

function closeModal() {
  show.value = false;
  emit("close");
  errors.value = {};
}

function onSubmit() {
  category.value.loading = true;
  category.value.active = !!category.value.active;
  if (category.value.id) {
    categoryStore
      .updateCategory(category.value)
      .then((response) => {
        category.value.loading = false;
        categoryStore.getCategories();
        closeModal();
      })
      .catch((err) => {
        category.value.loading = false;
        errors.value = err.response.data.errors;
        console.log("Error Message:", err.response.data);
        errors.value = err.response.data.errors;  
      });
  } else {
    categoryStore
      .createCategory(category.value)
      .then((response) => {
        category.value.loading = false;
        categoryStore.getCategories();
        closeModal();
      })
      .catch((err) => {
        category.value.loading = false;
        errors.value = err.response.data.errors;
        console.log("Error Message:", err.response.data);
        errors.value = err.response.data.errors;
      });
  }
}
</script>
