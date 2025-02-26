import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useUserStore = defineStore("user", () => {
    const user = ref({
        token: "123",
        data: {},
    });

    // const doubleCount = computed(() => count.value * 2);
    // function increment() {
    //     count.value++;
    // }

    return {
        user,
    };
});
