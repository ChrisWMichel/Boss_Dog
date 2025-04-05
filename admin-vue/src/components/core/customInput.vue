<template>
  <div>
    <label class="sr-only">{{ label }}</label>
    <div class="flex mt-1 rounded-md shadow-sm">
      <span
        v-if="prepend"
        class="inline-flex items-center px-3 text-gray-500 border border-r-0 border-gray-300 rounded-l-md bg-gray-50 sm:text-sm"
      >
        {{ prepend }}
      </span>
      <template v-if="type === 'select'">
        <select
          :id="id"
          :value="modelValue"
          :name="name"
          :required="required"
          :class="inputClasses"
          @change="updateValue"
        >
          <option v-for="option in selectOptions" :value="option.key">
            {{ option.text }}
          </option>
        </select>
      </template>
      <template v-if="type === 'textarea'">
        <textarea
          :id="id"
          :value="modelValue"
          :name="name"
          :required="required"
          :class="inputClasses + ' resize-y h-32'"
          :placeholder="label"
          rows="5"
          @input="updateValue"
        ></textarea>
      </template>
      <template v-else-if="type === 'checkbox'">
        <input
          type="checkbox"
          :checked="modelValue === true"
          :name="name"
          :required="required"
          :class="checkboxClasses"
          @change="updateCheckbox"
        />
      </template>
      <template v-else-if="type === 'file'">
        <input
          :id="id"
          type="file"
          :name="name"
          :required="required"
          :class="inputClasses"
          :placeholder="label"
          @change="updateFile"
        />
      </template>
      <template v-else>
        <input
          :type="type"
          :value="modelValue"
          :name="name"
          :required="required"
          :class="inputClasses"
          :placeholder="label"
          step="0.01"
          @input="updateValue"
        />
      </template>
      <span
        v-if="append"
        class="inline-flex items-center px-3 text-gray-500 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 sm:text-sm"
      >
        {{ append }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  modelValue: [String, Number, File, Boolean],
  type: {
    type: String,
    default: "text",
  },
  label: String,
  name: String,
  required: {
    type: Boolean,
    default: false,
  },
  selectOptions: Array,
  errors: {
    type: Array,
    required: false,
  },
  prepend: {
    type: String,
    default: "",
  },
  append: {
    type: String,
    default: "",
  },
  id: String,
});

const inputClasses = computed(() => {
  const cls = [
    "block w-full px-3 py-2 text-base placeholder-gray-400 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm",
  ];

  if (props.append && !props.prepend) {
    cls.push("rounded-l-md");
  } else if (props.prepend && !props.append) {
    cls.push("rounded-r-md");
  } else if (!props.prepend && !props.append) {
    cls.push("rounded-md");
  }
  return cls.join(" ");
});

const emit = defineEmits(["update:modelValue", "change"]);

const updateValue = (event) => {
  emit("update:modelValue", event.target.value);
};

const updateFile = (event) => {
  emit("update:modelValue", event.target.files[0]);
};

const checkboxClasses = computed(() => {
  return "h-4 w-4 mr-2 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded";
});

const updateCheckbox = (event) => {
  //console.log("Checkbox change event, checked:", event.target.checked);
  // console.log("Previous modelValue:", modelValue);
  emit("update:modelValue", event.target.checked);
  //console.log("Checkbox value updated to:", modelValue.value);
};

const id = computed(() => {
  if (props.id) return props.id;
  return `${props.name}-${Math.floor(1000000 + Math.random() * 10000000)}`;
});
</script>

<style scoped></style>
