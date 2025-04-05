<template>
  <div class="bg-white p-4 rounded-lg shadow animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="ml-3"
          >Found
          {{ categories.data && categories.data.length ? categories.data.length : 0 }}
          categories</span
        >
      </div>
    </div>

    <table class="table-auto w-full">
      <thead>
        <tr>
          <tableHeaderCell
            field="id"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortCategories('id')"
          >
            ID
          </tableHeaderCell>
          <tableHeaderCell
            field="name"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortCategories('name')"
          >
            Name
          </tableHeaderCell>
          <tableHeaderCell
            field="slug"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortCategories('slug')"
          >
            Slug
          </tableHeaderCell>
          <tableHeaderCell
            field="active"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortCategories('active')"
          >
            Active
          </tableHeaderCell>
          <tableHeaderCell
            field="parent_id"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortCategories('parent_id')"
          >
            Parent
          </tableHeaderCell>
          <tableHeaderCell
            field="created_at"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortCategories('created_at')"
          >
            Create Date
          </tableHeaderCell>
          <tableHeaderCell field="actions"> Actions </tableHeaderCell>
        </tr>
      </thead>
      <tbody v-if="categories.loading || !categories.data || !categories.data.length">
        <tr>
          <td colspan="7">
            <spinner v-if="categories.loading" />
            <p v-else class="text-center py-8 text-gray-700">There are no categories</p>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr v-for="(category, index) of categories.data">
          <td class="border-b p-2">{{ category.id }}</td>
          <td class="border-b p-2">
            {{ category.name }}
          </td>
          <td
            class="border-b p-2 max-w-[200px] whitespace-nowrap overflow-hidden text-ellipsis"
          >
            {{ category.slug }}
          </td>
          <td class="border-b p-2">
            {{ category.active ? "Active" : "Inactive" }}
          </td>
          <td class="border-b p-2">
            {{ category.parent?.name || "None" }}
          </td>
          <td class="border-b p-2">
            {{ category.created_at }}
          </td>
          <td class="border-b p-2">
            <Menu as="div" class="relative inline-block text-left">
              <div>
                <MenuButton
                  class="inline-flex items-center justify-center rounded-full w-10 h-10 bg-black bg-opacity-0 text-sm font-medium text-white hover:bg-opacity-5 focus:bg-opacity-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
                >
                  <EllipsisVerticalIcon
                    class="h-5 w-5 text-indigo-500"
                    aria-hidden="true"
                  />
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
                  class="absolute z-10 right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                  <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                        @click="editCategory(category)"
                      >
                        <PencilIcon
                          :active="active"
                          class="mr-2 h-5 w-5 text-indigo-400"
                          aria-hidden="true"
                        />
                        Edit
                      </button>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                        @click="deleteCategory(category)"
                      >
                        <TrashIcon
                          :active="active"
                          class="mr-2 h-5 w-5 text-indigo-400"
                          aria-hidden="true"
                        />
                        Delete
                      </button>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useCategoryStore } from "../../../store/useCategoryStore";
import spinner from "../../../src/components/core/spinner.vue";
import tableHeaderCell from "../../../src/components/core/Table/tableHeaderCell.vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { EllipsisVerticalIcon, PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

const categoryStore = useCategoryStore();
const categories = computed(() => categoryStore.categories);
const sortField = ref("name");
const sortDirection = ref("asc");

const category = ref({});
const showCategoryModal = ref(false);

const emit = defineEmits(["clickEdit"]);

onMounted(() => {
  getCategories();
});

function getForPage(ev, link) {
  ev.preventDefault();
  if (!link.url || link.active) {
    return;
  }

  getCategories(link.url);
}

function getCategories(url = null) {
  categoryStore.getCategories({
    url,
    sort_field: sortField.value,
    sort_direction: sortDirection.value,
  });
}

function sortCategories(field) {
  if (field === sortField.value) {
    if (sortDirection.value === "desc") {
      sortDirection.value = "asc";
    } else {
      sortDirection.value = "desc";
    }
  } else {
    sortField.value = field;
    sortDirection.value = "asc";
  }

  getCategories();
}

function showAddNewModal() {
  showCategoryModal.value = true;
}

function deleteCategory(category) {
  if (!confirm(`Are you sure you want to delete the category?`)) {
    return;
  }
  categoryStore.deleteCategory(category.id).then(() => {
    getCategories();
  });
}

function editCategory(p) {
  emit("clickEdit", p);
}
</script>

<style scoped></style>
