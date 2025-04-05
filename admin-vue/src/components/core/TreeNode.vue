<template>
  <div class="tree-node" :class="{'font-medium': node.children && node.children.length > 0}">
    <!-- Current node -->
    <label class="flex items-center px-3 py-1 hover:bg-gray-100 cursor-pointer">
      <input
        type="checkbox"
        :checked="isSelected"
        @change="toggleNode(node.id)"
        class="mr-2"
      />
      {{ node.text }}
    </label>

    <!-- Child nodes (if any) -->
    <div v-if="node.children && node.children.length > 0" class="pl-4 mt-1 ml-2 border-l border-gray-200 text-sm">
      <TreeNode
        v-for="child in node.children"
        :key="child.id"
        :node="child"
        :selected-ids="selectedIds"
        @toggle-node="toggleNode"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  node: {
    type: Object,
    required: true
  },
  selectedIds: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['toggle-node']);

// Check if this node is selected
const isSelected = computed(() => {
  return props.selectedIds.includes(props.node.id);
});

// Toggle node selection
const toggleNode = (nodeId) => {
  // Simply pass the nodeId to the parent component
  emit('toggle-node', nodeId);
};
</script>

<style scoped>
.tree-node {
  margin-bottom: 0.25rem;
}
</style>
