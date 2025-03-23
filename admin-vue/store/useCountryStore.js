import { defineStore } from "pinia";
import { ref } from "vue";
import axiosClient from "../src/axiosHelper.js";

export const useCountryStore = defineStore("country", () => {
    const countries = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const statesByCountry = ref({});

    async function getCountries() {
        if (countries.value.length) return countries.value;
        
        loading.value = true;
        error.value = null;
        
        try {
            const response = await axiosClient.get('/countries');
            countries.value = response.data;
            //console.log("Countries loaded:", countries.value);
            return countries.value;
        } catch (err) {
            console.error("Error fetching countries:", err);
            error.value = "Failed to load countries";
            return [];
        } finally {
            loading.value = false;
        }
    }

    async function getStates(countryCode) {
        // Return cached states if available
        if (statesByCountry.value[countryCode]) {
            return statesByCountry.value[countryCode];
        }
        
        loading.value = true;
        error.value = null;
        
        try {
            const response = await axiosClient.get(`/countries/${countryCode}/states`);
            statesByCountry.value[countryCode] = response.data;
            return response.data;
        } catch (err) {
            console.error(`Error fetching states for ${countryCode}:`, err);
            error.value = `Failed to load states for ${countryCode}`;
            return {};
        } finally {
            loading.value = false;
        }
    }

    return {
        countries,
        loading,
        error,
        statesByCountry,
        getCountries,
        getStates
    };
});