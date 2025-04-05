<template>
  <div class="tree-wrapper">
    <!-- Dropdown Categories Selector -->
    <div class="relative">
      <!-- Dropdown Button -->
      <button
        @click="toggleDropdown"
        type="button"
        class="w-full flex items-center justify-between px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <span>{{ selectedText }}</span>
        <svg
          class="h-5 w-5"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
        >
          <path
            fill-rule="evenodd"
            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </button>

      <!-- Dropdown Menu -->
      <div
        v-if="isOpen"
        class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
      >
        <!-- Hierarchical tree view implementation -->
        <div class="p-2">
          <div class="category-tree">
            <template v-for="node in nodes" :key="node.id">
              <TreeNode
                :node="node"
                :selected-ids="localValue"
                @toggle-node="toggleNode"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from "vue";
import TreeNode from "./TreeNode.vue";

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
  nodes: {
    type: Array,
    default: () => [],
  },
  placeholder: {
    type: String,
    default: "Select categories",
  },
});

const emit = defineEmits(["update:modelValue", "node-checked"]);

// Local copy of the value to avoid directly modifying props
const localValue = ref([...props.modelValue]);

// Dropdown state
const isOpen = ref(false);

// Computed property for the selected text
const selectedText = computed(() => {
  if (localValue.value.length === 0) {
    return props.placeholder;
  }

  // Get the names of selected categories
  const selectedNodes = props.nodes.filter((node) => localValue.value.includes(node.id));

  if (selectedNodes.length === 0) {
    return props.placeholder;
  } else if (selectedNodes.length === 1) {
    return selectedNodes[0].text;
  } else {
    return `${selectedNodes.length} categories selected`;
  }
});

// Toggle dropdown
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  const wrapper = document.querySelector(".tree-wrapper");
  if (wrapper && !wrapper.contains(event.target)) {
    isOpen.value = false;
  }
};

// Add/remove click outside listener
onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

// Watch for changes to the modelValue prop
watch(
  () => props.modelValue,
  (newValue) => {
    // Only update if the values are different to avoid circular updates
    if (JSON.stringify(localValue.value) !== JSON.stringify(newValue)) {
      localValue.value = [...newValue];
    }
  },
  { deep: true }
);

// Watch for changes to the local value and emit updates
watch(
  localValue,
  (newValue) => {
    // Only emit if the values are different to avoid circular updates
    if (JSON.stringify(props.modelValue) !== JSON.stringify(newValue)) {
      emit("update:modelValue", newValue);
    }
  },
  { deep: true }
);

// Find a node by ID in the tree
const findNodeById = (nodes, id) => {
  for (const node of nodes) {
    if (node.id === id) {
      return node;
    }
    if (node.children && node.children.length > 0) {
      const found = findNodeById(node.children, id);
      if (found) return found;
    }
  }
  return null;
};

// Get all parent IDs for a node
const getAllParentIds = (nodeId) => {
  const parentIds = [];
  let currentNode = null;

  // Find the node first
  const flattenNodes = (nodes) => {
    let result = [];
    for (const node of nodes) {
      result.push(node);
      if (node.children && node.children.length > 0) {
        result = result.concat(flattenNodes(node.children));
      }
    }
    return result;
  };

  const allNodes = flattenNodes(props.nodes);
  currentNode = allNodes.find((node) => node.id === nodeId);

  // Traverse up the parent chain
  while (currentNode && currentNode.parent_id) {
    parentIds.push(currentNode.parent_id);
    currentNode = allNodes.find((node) => node.id === currentNode.parent_id);
  }

  return parentIds;
};

// Toggle a node's selection
const toggleNode = (nodeId, isChecking = null) => {
  const index = localValue.value.indexOf(nodeId);
  // If isChecking is provided, use it; otherwise determine from the index
  const isChecked = isChecking !== null ? isChecking : index === -1;

  // Create a new array to avoid mutation issues
  const newValue = [...localValue.value];

  if (isChecked) {
    // Node is not selected, add it
    if (index === -1) {
      newValue.push(nodeId);
    }

    // Also select all parent nodes
    const parentIds = getAllParentIds(nodeId);
    for (const parentId of parentIds) {
      if (!newValue.includes(parentId)) {
        newValue.push(parentId);
      }
    }
  } else {
    // Node is selected, remove it
    if (index !== -1) {
      newValue.splice(index, 1);
    }
  }

  // Update the local value with the new array
  localValue.value = newValue;

  // Emit the node-checked event
  emit("node-checked", { id: nodeId, state: { checked: isChecked } });
};
</script>

<style scoped>
.tree-wrapper {
  width: 100%;
}
</style>
