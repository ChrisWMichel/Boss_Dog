<template>
  <div class="flex flex-wrap gap-2">
    <div v-for="image in imagesUrls" class="relative w-[120px] h-[120px] rounded overflow-hidden" :style="{ backgroundImage: `url(${image})`, backgroundSize: 'cover', backgroundPosition: 'center' }">
      <img :src="image" alt="" class="w-full h-full"/>
      <button class="absolute top-0 right-0 p-1 text-white bg-red-500 rounded-bl" @click="imagesUrls.splice(imagesUrls.indexOf(image), 1)">X</button>
    </div>
    <div class="relative w-[120px] h-[120px] rounded border border-dashed flex items-center justify-center hover:border-purple-500 overflow-hidden">
      <span >Upload images</span>
      <input type="file" multiple class="absolute top-0 bottom-0 left-0 right-0 w-full h-full opacity-0 cursor-pointer" @change="onFileChange"/>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, defineEmits, watch } from "vue";

const files = ref([]);
const imagesUrls = ref([]);

const props = defineProps({
  modelValue: Array,
  images: Array,
  deletedImages: Array,
  imagesPositions: Object,
});

const emit = defineEmits(["update:modelValue", "update:deletedImages", "update:imagesPositions"]);

const onFileChange = (event) => {
 
  files.value = [...files.value, ...event.target.files];
  for(let file of event.target.files){
    readFile(file)
    .then(url =>{
      imagesUrls.value.push(url);
    } )
  }
  emit("update:modelValue", files.value);
};

const readFile = (file) =>{
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = () => reject(reader.error);
    reader.readAsDataURL(file);
  });
}

onMounted(() => {
  emit("update:modelValue", []);
});

watch(() => props.images, () => {
  imagesUrls.value = [
    ...imagesUrls.value.filter(im => im.isPropImage),
    ...props.images.map(im => ({
      ...im,
      isPropImage: true,
    }))
  ]
});
</script>

<style scoped></style>
