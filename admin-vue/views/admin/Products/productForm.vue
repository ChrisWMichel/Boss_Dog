<!--suppress HtmlUnknownAttribute -->

<template>
  <div class="flex items-center justify-between mb-3">
    <h1 v-if="!loading" class="text-3xl font-semibold">
      {{ product.id ? `Update product: "${product.title}"` : "Create new Product" }}
    </h1>
  </div>
  <!-- <div class="bg-white rounded-lg shadow animate-fade-in-down"> -->
  <div class="flex justify-center p-4 bg-white rounded-lg shadow animate-fade-in-down">
    <spinner
      v-if="loading"
      class="absolute left-0 top-0 bg-white right-0 bottom-0 flex items-center justify-center z-50"
    />
    <form v-if="!loading" @submit.prevent="onSubmit" class="bg-gray-200 w-3/4 rounded-md">
      <div class="grid grid-cols-3">
        <div class="col-span-2 px-4 pt-5 pb-4">
          <customInput
            class="mb-2"
            v-model="product.title"
            label="Product Title"
            :errors="errors['title']"
          />
          <customInput
            type="richtext"
            class="mb-2"
            v-model="product.description"
            label="Description"
            :errors="errors['description']"
          />
          <!-- <Editor v-model="product.description" /> -->

          <customInput
            type="number"
            class="mb-2"
            v-model="product.price"
            label="Price"
            prepend="$"
            :errors="errors['price']"
          />
          <customInput
            type="number"
            class="mb-2"
            v-model="product.quantity"
            label="Quantity"
            :errors="errors['quantity']"
          />
          <div class="flex mb-4">
            <customInput
              type="checkbox"
              class="mb-2"
              v-model="product.published"
              label="Published"
              :errors="errors['published']"
            />
            <span>Published</span>
          </div>
          <treeselect
            v-model="product.categories"
            :multiple="true"
            :options="options"
            :errors="errors['categories']"
          />
        </div>
        <div class="col-span-1 px-4 pt-5 pb-4">
          <images-preview
            v-model="product.images"
            :images="product.images"
            v-model:deleted-images="product.deleted_images"
            v-model:images-positions="product.image_positions"
          />
        </div>
      </div>
      <footer
        class="bg-gray-100 rounded-b-lg px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
      >
        <button
          type="submit"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500"
        >
          Save
        </button>
        <button
          type="button"
          @click="onSubmit($event, true)"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500"
        >
          Save & Close
        </button>
        <router-link
          :to="{ name: 'app.products' }"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          ref="cancelButtonRef"
        >
          Cancel
        </router-link>
      </footer>
    </form>
  </div>
  <!-- </div> -->
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import customInput from "../../../src/components/core/customInput.vue";
import spinner from "../../../src/components/core/spinner.vue";
import { useRoute, useRouter } from "vue-router";
import ImagePreview from "../../../src/components/core/ImagePreview.vue";
//import Treeselect from "vue3-treeselect";
import { useProductStore } from "../../../store/useProductStore.js";
//import "vue3-treeselect/dist/vue3-treeselect.css";
import axiosClient from "../../../src/axiosHelper";
import { useToast } from "vue-toastification";
import Editor from "../../../src/components/core/Editor.vue";

const route = useRoute();
const router = useRouter();
const productStore = useProductStore();
const toast = useToast();

const product = ref({
  id: null,
  title: null,
  images: [],
  deleted_images: [],
  image_positions: {},
  description: "",
  price: null,
  quantity: null,
  published: false,
  categories: [],
});

const errors = ref({});

const loading = ref(false);
const options = ref([]);

const emit = defineEmits(["update:modelValue", "close"]);

// const props = defineProps({
//   id: {
//     type: [Number, String],
//     default: null,
//   },
// });

onMounted(async () => {
  if (route.params.id) {
    loading.value = true;
    product.value = await productStore.getProductData(route.params.id);
    //console.log("Product loaded:", product.value);
    loading.value = false;
  }

  // axiosClient.get("/categories/tree").then((result) => {
  //   options.value = result.data;
  // });
});

const onSubmit = async () => {
  loading.value = true;
  product.value.quantity = product.value.quantity || null;
  if (product.value.id) {
    const hasValidImage =
      product.value.images instanceof File && product.value.images.size > 0;
    const productData = {
      ...product.value,
      imageUpdated: hasValidImage || imageUpdated.value,
    };
    await productStore
      .updateProduct(productData)
      .then(() => {
        imageUpdated.value = false;
        loading.value = false;
        product.value = {
          id: "",
          title: "",
          price: "",
          published: true,
          description: "",
          images: null,
        };
        toast.success("Product has been successfully updated");
        if (close) {
          router.push({ name: "app.products" });
        }
      })
      .catch((error) => {
        console.error("Error updating product:", error);
        loading.value = false;
      });
  } else {
    try {
      await productStore.createProduct(product.value).then(() => {
        loading.value = false;
        product.value = {
          id: "",
          title: "",
          price: "",
          published: "",
          description: "",
          images: null,
        };
        toast.success("Product has been successfully created");
        if (close) {
          router.push({ name: "app.products" });
        }
      });
    } catch (error) {
      console.error("Error submitting form:", error);
      loading.value = false;
    }
  }
};

// watch(
//   () => props.product,
//   (newProduct) => {
//     const publishedValue = newProduct.published === true;
//     product.value = {
//       id: newProduct.id || "",
//       title: newProduct.title || "",
//       price: newProduct.price || "",
//       published: publishedValue, // Explicitly convert to boolean
//       description: newProduct.description || "",
//       images: newProduct.images || null,
//     };
//   },
//   { immediate: true }
// );
</script>
