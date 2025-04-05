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
          <div>
            <p class="mb-2 font-medium">Categories:</p>
            <div v-if="treeNodes.length === 0" class="text-gray-500 italic">
              No categories available
            </div>

            <!-- Categories dropdown -->
            <TreeWrapper
              v-model="product.categories"
              :nodes="treeNodes"
              @node-checked="handleNodeChecked"
              class="mb-20"
            />
            <div v-if="errors['categories']" class="text-red-500 text-sm mt-1">
              {{ errors["categories"] }}
            </div>
          </div>
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
import { onMounted, ref } from "vue";
import customInput from "../../../src/components/core/customInput.vue";
import spinner from "../../../src/components/core/spinner.vue";
import { useRoute, useRouter } from "vue-router";
import ImagePreview from "../../../src/components/core/ImagePreview.vue";
import TreeWrapper from "../../../src/components/core/TreeWrapper.vue";
import { useProductStore } from "../../../store/useProductStore.js";
import { useCategoryStore } from "../../../store/useCategoryStore.js";
import axiosClient from "../../../src/axiosHelper";
import { useToast } from "vue-toastification";

const route = useRoute();
const router = useRouter();
const productStore = useProductStore();
const categoryStore = useCategoryStore();
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

// Tree nodes for categories
const treeNodes = ref([]);

const emit = defineEmits(["update:modelValue", "close"]);

const props = defineProps({
  id: {
    type: [Number, String],
    default: null,
  },
});

onMounted(async () => {
  loading.value = true;

  // Fetch categories
  await categoryStore.getCategories();

  // Convert categories to hierarchical tree nodes format
  if (categoryStore.categories.data && categoryStore.categories.data.length > 0) {
    // First, create a map of all categories
    const categoriesMap = {};
    categoryStore.categories.data.forEach((category) => {
      categoriesMap[category.id] = {
        id: category.id,
        text: category.name,
        parent_id: category.parent_id,
        children: [],
        state: {
          checked: false,
          expanded: true,
          selected: false,
          disabled: false,
          editable: false,
          draggable: false,
          dropable: false,
        },
      };
    });

    // Then, build the tree structure
    const rootNodes = [];

    // Add each category to its parent's children array
    Object.values(categoriesMap).forEach((node) => {
      if (node.parent_id) {
        // This is a child node
        if (categoriesMap[node.parent_id]) {
          categoriesMap[node.parent_id].children.push(node);
        } else {
          // Parent not found, add to root
          rootNodes.push(node);
        }
      } else {
        // This is a root node
        rootNodes.push(node);
      }
    });

    // Set the tree nodes
    treeNodes.value = rootNodes;
  }

  // If editing an existing product
  if (route.params.id) {
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
    // Ensure categories property exists and has values
    if (!product.value.categories || product.value.categories.length === 0) {
      product.value.categories = [];

      // Fetch categories for this product
      try {
        const response = await axiosClient.get(
          `/products/${product.value.id}/categories`
        );
        if (response.data && Array.isArray(response.data)) {
          product.value.categories = response.data;
        } else {
          // For testing: If we have tree nodes but no categories, let's add all categories
          if (treeNodes.value && treeNodes.value.length > 0) {
            // Initialize categories as an empty array
            product.value.categories = [];

            // Only mark categories that are in product.categories as checked
            treeNodes.value.forEach((node) => {
              node.state.checked = product.value.categories.includes(node.id);
            });
          }
        }
      } catch (error) {
        console.error("Error fetching product categories:", error);

        // For testing: If API call fails, use tree nodes
        if (treeNodes.value && treeNodes.value.length > 0) {
          product.value.categories = [];

          // Only mark categories that are in product.categories as checked
          treeNodes.value.forEach((node) => {
            node.state.checked = product.value.categories.includes(node.id);
          });
        }
      }
    }

    // Check the categories in the tree that are associated with this product
    if (product.value.categories && product.value.categories.length > 0) {
      const categoryIds = product.value.categories.map((cat) =>
        typeof cat === "object" ? cat.id : cat
      );

      // Update the checked state of tree nodes
      treeNodes.value.forEach((node) => {
        if (categoryIds.includes(node.id)) {
          node.state.checked = true;
        }
      });

      // Make sure product.categories contains only IDs, not objects
      product.value.categories = categoryIds;
    }

    // Ensure properties exist after loading
    ensureProductProperties();
  }

  loading.value = false;
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

  // Ensure categories exists and is an array
  if (!product.value.categories) {
    product.value.categories = [];
    console.log(
      "Categories property was missing in ensureProductProperties, initialized to empty array"
    );
  }

  // For testing: If categories array is empty and we have tree nodes, add all categories
  if (
    product.value.categories.length === 0 &&
    treeNodes.value &&
    treeNodes.value.length > 0
  ) {
    // Only do this for existing products (with an ID), not for new products
    if (product.value.id) {
      product.value.categories = [];

      // Only mark categories that are in product.categories as checked
      treeNodes.value.forEach((node) => {
        node.state.checked = product.value.categories.includes(node.id);
      });
    }
  }
};

const onSubmit = async (_, close = false) => {
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
            image_positions: {},
          };
          router.push({ name: "app.products" });
        } else {
          // Reload the product data to refresh the form
          productStore.getProductData(product.value.id).then((productData) => {
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

// Handle when a category is checked/unchecked in the tree
// This function is just a placeholder for future functionality if needed
const handleNodeChecked = () => {};

const handleImageDeleted = async ({ filename }) => {
  try {
    // Get the image ID from the server
    const response = await axiosClient.post("/get-image-id", {
      filename: filename,
      product_id: product.value.id,
    });

    if (response.data && response.data.id) {
      const imageId = response.data.id;

      // Create an object with the filename and the actual ID
      const imageInfo = {
        id: imageId,
        filename: filename,
      };

      // Add the image info to deleted_images
      if (!product.value.deleted_images) {
        product.value.deleted_images = [];
      }

      // Check if this image is already in the array
      const exists = product.value.deleted_images.some(
        (item) => typeof item === "object" && item.id === imageInfo.id
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
