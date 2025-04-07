<template>
  <div class="flex flex-wrap gap-1">
    <Sortable
      :list="imagesUrls"
      item-key="id"
      class="flex flex-wrap gap-1"
      @end="onImageDragEnd"
    >
      <template #item="{element: image, index}">
        <div
          :data-id="image.id"
          class="relative w-[120px] h-[120px] rounded border border-dashed flex items-center justify-center hover:border-purple-500 overflow-hidden">
          <img :src="image.url" class="max-w-full max-h-full" :class="image.deleted ? 'opacity-50' : ''" @error="handleImageError($event, image)">
          <small v-if="image.deleted"
                 class="absolute bottom-0 left-0 right-0 flex items-center justify-between px-2 py-1 text-white bg-black w-100">
            To be deleted
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-4 h-4 cursor-pointer" @click="revertImage(image)">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
            </svg>
          </small>
          <span class="absolute px-1 text-white bg-red-600 rounded-full cursor-pointer top-1 right-1" @click="removeImage(image)">
            X
          </span>
        </div>
      </template>
    </Sortable>
    <div
      class="relative w-[120px] h-[120px] rounded border border-dashed flex items-center justify-center hover:border-purple-500 overflow-hidden">
      <span>
        Upload
      </span>
      <input type="file" class="absolute top-0 bottom-0 left-0 right-0 w-full h-full opacity-0"
             @change="onFileChange" multiple accept="image/*">
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, defineEmits, watch, computed } from "vue";
import { Sortable } from "sortablejs-vue3";

const files = ref([]);
const imagesUrls = ref([]);
const deletedImages = ref([]);
const imagePositions = ref({});

const handleImageError = (event, image) => {
  console.error("Failed to load image:", image.url);
};

const imagesList = computed(() => {
  return imagesUrls.value.map((url, index) => {
    // Check if the URL is already an object
    if (typeof url === 'object' && url.url) {
      return url;
    }
    
    // Create a new object with the URL
    return {
      id: index,
      url: url,
      deleted: false,
      isProp: false
    };
  });
});

const sortedImages = computed(() => {
  const images = [...imagesList.value];
  
  // If we have positions, sort the images
  if (props.imagesPositions && Object.keys(props.imagesPositions).length > 0) {

    
    images.sort((a, b) => {
      const posA = props.imagesPositions[a.id] !== undefined ? props.imagesPositions[a.id] : 999;
      const posB = props.imagesPositions[b.id] !== undefined ? props.imagesPositions[b.id] : 999;
      
      // If both have positions, sort by position
      if (posA !== 999 && posB !== 999) {
        return posA - posB;
      }
      
      // If only one has a position, prioritize it
      if (posA !== 999) return -1;
      if (posB !== 999) return 1;
      
      // If neither has a position, maintain original order
      return 0;
    });
  }
  
  return images;
});

const props = defineProps({
  modelValue: Array,
  images: Array,
  deletedImages: Array,
  imagesPositions: Object,
});

const emit = defineEmits([
  "update:modelValue",
  "update:deletedImages",
  "update:imagesPositions",
  "image-deleted",
]);

const onFileChange = (event) => { 
  // Add new files to the files array
  const newFiles = Array.from(event.target.files);
  files.value = [...files.value, ...newFiles];
  
  // Process all files to get their URLs
  const filePromises = newFiles.map((file) => readFile(file));
  
  Promise.all(filePromises).then((newUrls) => {    
    // Add new URLs to imagesUrls
    newUrls.forEach(url => {
      // Create a proper image object for each URL
      const newImage = {
        id: Date.now() + Math.random().toString(36).substring(2, 9), // Generate a unique ID
        url: url,
        deleted: false,
        isProp: false
      };
      
      imagesUrls.value.push(newImage);
    });
    
    // Emit the updated files array
    emit("update:modelValue", files.value);
  });
};

const readFile = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = () => reject(reader.error);
    reader.readAsDataURL(file);
  });
};

function revertImage(image) {
  if (image.isProp) {
    deletedImages.value = deletedImages.value.filter(id => id !== image.id);
    image.deleted = false;

    emit('update:deletedImages', deletedImages.value);
  }
}

const removeImage = (image) => {
  // Get the image URL
  const imageUrl = image.url;
  
  // Remove from imagesUrls array
  const index = imagesUrls.value.findIndex(url => {
    if (typeof url === 'string') {
      return url === imageUrl;
    } else if (typeof url === 'object' && url.url) {
      return url.url === imageUrl;
    }
    return false;
  });
  
  if (index !== -1) {
    imagesUrls.value.splice(index, 1);
  }

  // Find and remove the corresponding file from files array
  const filePromises = files.value.map(async (file, idx) => {
    const url = await readFile(file);
    return { file, url, idx };
  });

  Promise.all(filePromises).then((results) => {
    const fileToRemove = results.find((item) => item.url === imageUrl);
    if (fileToRemove) {
      files.value.splice(fileToRemove.idx, 1);
      emit("update:modelValue", files.value);
    }

    // Extract the filename from the URL
    const urlParts = imageUrl.split("/");
    const filename = urlParts[urlParts.length - 1];
    
    // If the image is a prop (from the server), mark it as deleted
    if (image.isProp) {
      image.deleted = true;
      if (!deletedImages.value.includes(image.id)) {
        deletedImages.value.push(image.id);
        emit("update:deletedImages", deletedImages.value);
      }
    } else {
      // Emit the image-deleted event with the full URL and filename
      emit("image-deleted", { url: imageUrl, filename });
    }
  });
};

// Handle the end of drag event to update image positions
const onImageDragEnd = (event) => {
  
  // Get the old and new indices
  const { oldIndex, newIndex } = event;
  
  // Get the IDs of all images in the current order
  const imageIds = [];
  imagesUrls.value.forEach((image, index) => {
    let id;
    if (typeof image === 'object' && image.id) {
      id = image.id;
    } else if (typeof image === 'string' && image.includes('/')) {
      // Try to extract ID from URL
      const urlParts = image.split('/');
      const filename = urlParts[urlParts.length - 1];
      const matches = filename.match(/^(\d+)_/);
      if (matches && matches[1]) {
        id = parseInt(matches[1]);
      }
    }
    if (id) {
      imageIds[index] = id;
    }
  });
  
  // Create a new array with the updated order
  const newOrder = [...imageIds];
  
  // Move the item from oldIndex to newIndex
  const [movedItem] = newOrder.splice(oldIndex, 1);
  newOrder.splice(newIndex, 0, movedItem);
  
  // Create new positions object based on the new order
  const newPositions = {};
  newOrder.forEach((id, index) => {
    if (id) {
      newPositions[id] = index;
    }
  });
  
  // Emit to parent
  emit("update:imagesPositions", newPositions);
};



// Initialize the component
onMounted(() => {  
  // Initialize imagesUrls with existing images from props
  if (props.images && Array.isArray(props.images) && props.images.length > 0) {
    imagesUrls.value = props.images.map(image => {
      if (typeof image === 'string') {
        return image;
      } else if (typeof image === 'object') {
        // Mark images from props
        return {
          id: image.id,
          url: image.url || '',
          deleted: false,
          isProp: true
        };
      }
      return null;
    }).filter(img => img !== null);
    
    // Sort the images based on positions if available
    if (props.imagesPositions && Object.keys(props.imagesPositions).length > 0) {
      imagesUrls.value.sort((a, b) => {
        // Get the IDs of the images
        const idA = typeof a === 'object' && a.id ? a.id : null;
        const idB = typeof b === 'object' && b.id ? b.id : null;
        
        // If both have IDs and positions, sort by position
        if (idA && idB && props.imagesPositions[idA] !== undefined && props.imagesPositions[idB] !== undefined) {
          return props.imagesPositions[idA] - props.imagesPositions[idB];
        }
        
        // If only one has a position, prioritize it
        if (idA && props.imagesPositions[idA] !== undefined) return -1;
        if (idB && props.imagesPositions[idB] !== undefined) return 1;
        
        // If neither has a position, maintain original order
        return 0;
      });
    }
  }
  
  // Initialize deletedImages from props
  if (props.deletedImages && Array.isArray(props.deletedImages)) {
    deletedImages.value = [...props.deletedImages];
  }
  
  // Only initialize with empty array if there are no files yet
  if (files.value.length === 0) {
    emit("update:modelValue", []);
  }
});

// Watch for changes to props.images
watch(
  () => props.images,
  (newImages) => {    
    // Only update if newImages exists and is an array
    if (newImages && Array.isArray(newImages)) {
      // Convert images to objects with proper structure
      const imageObjects = newImages.map(image => {
        if (typeof image === 'string') {
          return {
            url: image,
            deleted: false,
            isProp: true
          };
        } else if (typeof image === 'object' && image.url) {
          return {
            id: image.id,
            url: image.url,
            deleted: false,
            isProp: true
          };
        }
        return null;
      }).filter(img => img !== null);
      
      // Update imagesUrls with both existing images and local files
      if (imageObjects.length > 0) {
        // Start with image objects from props
        imagesUrls.value = [...imageObjects];
        
        // Add URLs from local files
        const filePromises = files.value.map((file) => readFile(file));
        
        Promise.all(filePromises).then((fileUrls) => {
          fileUrls.forEach(url => {
            const exists = imagesUrls.value.some(img => {
              if (typeof img === 'string') {
                return img === url;
              } else if (typeof img === 'object' && img.url) {
                return img.url === url;
              }
              return false;
            });
            
            if (!exists) {
              imagesUrls.value.push({
                url: url,
                deleted: false,
                isProp: false
              });
            }
          });
        });
      }
    }
  }
);

// Watch for changes to props.deletedImages
watch(
  () => props.deletedImages,
  (newDeletedImages) => {
    if (newDeletedImages && Array.isArray(newDeletedImages)) {
      deletedImages.value = [...newDeletedImages];
      
      // Mark deleted images
      imagesUrls.value.forEach(img => {
        if (typeof img === 'object' && img.id && deletedImages.value.includes(img.id)) {
          img.deleted = true;
        }
      });
    }
  }
);

watch(
  () => props.imagesPositions,
  (newPositions) => {
    if (newPositions && typeof newPositions === 'object') {
      
      // Sort the imagesUrls array based on the new positions
      if (Object.keys(newPositions).length > 0) {
        // Create a copy of the array to avoid modifying the original during sort
        const sortedUrls = [...imagesUrls.value];
        
        // Sort the array based on the positions
        sortedUrls.sort((a, b) => {
          // Get the IDs of the images
          const idA = typeof a === 'object' && a.id ? a.id : null;
          const idB = typeof b === 'object' && b.id ? b.id : null;
          
          // If both have IDs and positions, sort by position
          if (idA && idB && newPositions[idA] !== undefined && newPositions[idB] !== undefined) {
            return newPositions[idA] - newPositions[idB];
          }
          
          // If only one has a position, prioritize it
          if (idA && newPositions[idA] !== undefined) return -1;
          if (idB && newPositions[idB] !== undefined) return 1;
          
          // If neither has a position, maintain original order
          return 0;
        });
        
        // Update the imagesUrls array with the sorted array
        imagesUrls.value = sortedUrls;
      }
    }
  },
  { deep: true }
);
</script>

<style scoped></style>