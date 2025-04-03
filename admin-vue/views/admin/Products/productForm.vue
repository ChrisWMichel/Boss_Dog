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
      class="absolute top-0 bottom-0 left-0 right-0 z-50 flex items-center justify-center bg-white"
    />
    <form v-if="!loading" @submit.prevent="onSubmit" class="w-3/4 bg-gray-200 rounded-md">
      <div class="grid grid-cols-3">
        <div class="col-span-2 px-4 pt-5 pb-4">
          <customInput
            class="mb-2"
            v-model="product.title"
            label="Product Title"
            :errors="errors['title']"
          />
          <customInput
            type="textarea"
            class="mb-2"
            v-model="product.description"
            label="Description"
            :errors="errors['description']"
          />

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
          <!-- <treeselect
            v-model="product.categories"
            :multiple="true"
            :options="options"
            :errors="errors['categories']"
          /> -->
        </div>

        <div class="col-span-1 px-4 pt-5 pb-4">
          <ImagePreview
            v-model="product.images"
            :images="product.images"
            v-model:deleted-images="product.deleted_images"
            v-model:images-positions="product.image_positions"
            @image-deleted="handleImageDeleted"
          />
        </div>
      </div>
      <footer
        class="px-4 py-3 bg-gray-100 rounded-b-lg sm:px-6 sm:flex sm:flex-row-reverse"
      >
        <button
          type="submit"
          class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-white bg-indigo-600 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm hover:bg-indigo-700 focus:ring-indigo-500"
        >
          Save
        </button>
        <button
          type="button"
          @click="onSubmit($event, true)"
          class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-white bg-indigo-600 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm hover:bg-indigo-700 focus:ring-indigo-500"
        >
          Save & Close
        </button>
        <router-link
          :to="{ name: 'app.products' }"
          class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
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
  published: true,
  categories: [],
});

const errors = ref({});

const loading = ref(false);
const options = ref([]);

const emit = defineEmits(["update:modelValue", "close"]);

const props = defineProps({
  id: {
    type: [Number, String],
    default: null,
  },
});

onMounted(async () => {
  if (route.params.id) {
    loading.value = true;
    const productData = await productStore.getProductData(route.params.id);

    // Ensure images is an array of objects with id and url properties
    if (productData.images) {

      // Format images if needed
      const formattedImages = productData.images
        .map((img) => {
          if (typeof img === "string") {
            return { url: img };
          } else if (typeof img === "object") {
            return img;
          }
          return null;
        })
        .filter((img) => img !== null);

      productData.images = formattedImages;
    }

    product.value = productData;

    // Ensure properties exist after loading
    ensureProductProperties();

    loading.value = false;
  }
});

const ensureProductProperties = () => {
  // Ensure deleted_images exists and is an array
  if (!product.value.deleted_images) {
    product.value.deleted_images = [];
  }

  // Ensure image_positions exists and is an object
  if (!product.value.image_positions) {
    product.value.image_positions = {};
  }
};

const onSubmit = async (event, close = false) => {
  ensureProductProperties();
  loading.value = true;
  product.value.quantity = product.value.quantity || null;

  if (product.value.id) {
    const productData = {
      ...product.value,
    };
    console.log("Submitting product data:", productData);
    await productStore
      .updateProduct(productData)
      .then(() => {
        loading.value = false;
        toast.success("Product has been successfully updated");

        if (close) {
          // Reset the form and redirect
          product.value = {
            id: "",
            title: "",
            price: "",
            published: true,
            description: "",
            images: [],
            deleted_images: [],
            image_positions: {}
          };
          router.push({ name: "app.products" });
        } else {
          // Reload the product data to refresh the form
          productStore.getProductData(product.value.id).then(productData => {
            product.value = productData;
            // Reset deleted_images after successful update
            product.value.deleted_images = [];
            // Ensure properties exist
            ensureProductProperties();
          });
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
          published: true,
          description: "",
          images: null,
        };
        toast.success("Product has been successfully created.");
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

const handleImageDeleted = async ({ url, filename }) => {
  try {
    // Get the image ID from the server
    const response = await axiosClient.post('/get-image-id', {
      filename: filename,
      product_id: product.value.id
    });

    if (response.data && response.data.id) {
      const imageId = response.data.id;

      // Create an object with the filename and the actual ID
      const imageInfo = {
        id: imageId,
        filename: filename
      };

      // Add the image info to deleted_images
      if (!product.value.deleted_images) {
        product.value.deleted_images = [];
      }

      // Check if this image is already in the array
      const exists = product.value.deleted_images.some(item =>
        (typeof item === 'object' && item.id === imageInfo.id)
      );

      if (!exists) {
        product.value.deleted_images.push(imageInfo);
      }
    } else {
      console.log("Could not get image ID from server");
    }
  } catch (error) {
    console.error("Error getting image ID:", error);
  }
};
</script>
