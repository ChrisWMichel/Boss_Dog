<template>
  <div v-if="customer.id || isLoading" class="animate-fade-in-down">
    <form @submit.prevent="onSubmit">
      <div class="px-4 pt-5 pb-4 bg-white">
        <h1 class="pb-2 text-2xl font-semibold">{{ title }}</h1>
        <custom-input
          class="mb-2"
          v-model="customer.first_name"
          label="First Name"
          :errors="errors.first_name"
          type="text"
          :value="customer.first_name"
        />
        <custom-input
          class="mb-2"
          v-model="customer.last_name"
          label="Last Name"
          :errors="errors.last_name"
        />
        <custom-input
          class="mb-2"
          v-model="customer.email"
          label="Email"
          :errors="errors.email"
        />
        <custom-input
          class="mb-2"
          v-model="customer.phone"
          label="Phone"
          :errors="errors.phone"
        />
        <custom-input
          type="checkbox"
          class="mb-2"
          v-model="customer.status"
          label="Active"
          :errors="errors.status"
        />

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <div>
            <h2 class="pb-2 mt-6 text-xl font-semibold border-b border-gray-300">
              Billing Address
            </h2>

            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
              <custom-input
                v-model="customer.billingAddress.address1"
                label="Address 1"
                :errors="errors['billingAddress.address1']"
              />
              <custom-input
                v-model="customer.billingAddress.address2"
                label="Address 2"
                :errors="errors['billingAddress.address2']"
              />
              <custom-input
                v-model="customer.billingAddress.city"
                label="City"
                :errors="errors['billingAddress.city']"
              />
              <custom-input
                v-model="customer.billingAddress.zipcode"
                label="Zip Code"
                :errors="errors['billingAddress.zipcode']"
              />

              <custom-input
                type="select"
                :select-options="countries"
                v-model="customer.billingAddress.country_code"
                label="Country"
                :errors="errors['billingAddress.country_code']"
              />
              <custom-input
                v-if="billingCountry && !billingCountry.states"
                v-model="customer.billingAddress.state"
                label="State"
                :errors="errors['billingAddress.state']"
              />
              <custom-input
                v-else
                type="select"
                :select-options="billingStateOptions"
                v-model="customer.billingAddress.state"
                label="State"
                :errors="errors['billingAddress.state']"
              />
            </div>
          </div>

          <div>
            <h2 class="pb-2 mt-6 text-xl font-semibold border-b border-gray-300">
              Shipping Address
            </h2>

            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
              <custom-input
                v-model="customer.shippingAddress.address1"
                label="Address 1"
                :errors="errors['shippingAddress.address1']"
              />
              <custom-input
                v-model="customer.shippingAddress.address2"
                label="Address 2"
                :errors="errors['shippingAddress.address2']"
              />
              <custom-input
                v-model="customer.shippingAddress.city"
                label="City"
                :errors="errors['shippingAddress.city']"
              />
              <custom-input
                v-model="customer.shippingAddress.zipcode"
                label="Zip Code"
                :errors="errors['shippingAddress.zipcode']"
              />
              <custom-input
                type="select"
                :select-options="countries"
                v-model="customer.shippingAddress.country_code"
                label="Country"
                :errors="errors['shippingAddress.country_code']"
              />
              <custom-input
                v-if="shippingCountry && !shippingCountry.states"
                v-model="customer.shippingAddress.state"
                label="State"
                :errors="errors['shippingAddress.state']"
              />
              <custom-input
                v-else
                type="select"
                :select-options="shippingStateOptions"
                v-model="customer.shippingAddress.state"
                label="State"
                :errors="errors['shippingAddress.state']"
              />
            </div>
          </div>
        </div>
      </div>
      <footer class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
        <button
          type="submit"
          class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
        >
          Submit
        </button>
        <router-link
          :to="{ name: 'app.customers' }"
          type="button"
          class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          ref="cancelButtonRef"
        >
          Cancel
        </router-link>
      </footer>
    </form>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { useCustomerStore } from "../../../store/useCustomerStore";
import { useCountryStore } from "../../../store/useCountryStore";
import { useRoute, useRouter } from "vue-router";
import CustomInput from "../../../src/components/core/CustomInput.vue";
import { useToast } from "vue-toastification";

const router = useRouter();
const route = useRoute();
const storeCustomer = useCustomerStore();
const countryStore = useCountryStore();
const isLoading = ref(false);
const toast = useToast();

const customer = ref({
  billingAddress: {},
  shippingAddress: {},
});

const title = ref("");
const errors = ref({
  first_name: [],
  last_name: [],
  email: [],
  phone: [],
  status: [],
  "billingAddress.address1": [],
  "billingAddress.address2": [],
  "billingAddress.city": [],
  "billingAddress.zipcode": [],
  "billingAddress.country_code": [],
  "billingAddress.state": [],
  "shippingAddress.address1": [],
  "shippingAddress.address2": [],
  "shippingAddress.city": [],
  "shippingAddress.zipcode": [],
  "shippingAddress.country_code": [],
  "shippingAddress.state": [],
});

const loading = ref(false);

const countries = computed(() => {
  if (!countryStore.countries || !Array.isArray(countryStore.countries)) {
    return [];
  }
  return countryStore.countries.map((c) => ({ key: c.code, text: c.name }));
});

const billingCountry = computed(() => {
  if (!countryStore.countries || !Array.isArray(countryStore.countries)) {
    return null;
  }
  return countryStore.countries.find(
    (c) => c.code === customer.value.billingAddress.country_code
  );
});

const billingStateOptions = computed(() => {
  if (!billingCountry.value) return [];

  const states = countryStore.statesByCountry[customer.value.billingAddress.country_code];
  if (!states) return [];

  return Object.entries(states).map((c) => ({
    key: c[0],
    text: c[1],
  }));
});

const shippingCountry = computed(() => {
  if (!countryStore.countries || !Array.isArray(countryStore.countries)) {
    return null;
  }
  return countryStore.countries.find(
    (c) => c.code === customer.value.shippingAddress.country_code
  );
});

const shippingStateOptions = computed(() => {
  if (!shippingCountry.value) return [];

  const states =
    countryStore.statesByCountry[customer.value.shippingAddress.country_code];
  if (!states) return [];

  return Object.entries(states).map((c) => ({
    key: c[0],
    text: c[1],
  }));
});

function onSubmit() {
  loading.value = true;
  if (customer.value.id) {
    // Convert status to boolean
    customer.value.status = !!customer.value.status;

    storeCustomer
      .updateCustomer(customer.value)
      .then(() => {
        loading.value = false;
        toast.success("Customer has been successfully updated");
        storeCustomer.getCustomers();
        router.push({ name: "app.customers" });
      })
      .catch((err) => {
        loading.value = false;
        if (err.response && err.response.data && err.response.data.errors) {
          errors.value = err.response.data.errors;
        }
        toast.error("Failed to update customer");
      });
  } else {
    storeCustomer
      .createCustomer(customer.value)
      .then(() => {
        loading.value = false;
        toast.success("Customer has been successfully created");
        storeCustomer.getCustomers();
        router.push({ name: "app.customers" });
      })
      .catch((err) => {
        loading.value = false;
        if (err.response && err.response.data && err.response.data.errors) {
          errors.value = err.response.data.errors;
        }
        toast.error("Failed to create customer");
      });
  }
}

// Add watchers to load states when country changes
watch(
  () => customer.value?.billingAddress?.country_code,
  (newCountryCode) => {
    if (newCountryCode) {
      countryStore.getStates(newCountryCode);
    }
  }
);

watch(
  () => customer.value?.shippingAddress?.country_code,
  (newCountryCode) => {
    if (newCountryCode) {
      countryStore.getStates(newCountryCode);
    }
  }
);

onMounted(async () => {
  // Load countries on component mount
  await countryStore.getCountries();

  if (route.params.id) {
    isLoading.value = true;
    try {
      const response = await storeCustomer.getCustomerData(route.params.id);
      const data = response.data.data || response.data;

      // Initialize customer with basic data
      customer.value = {
        id: data.id,
        first_name: data.first_name,
        last_name: data.last_name,
        email: data.email,
        phone: data.phone,
        status: data.status,
        billingAddress: data.billing_address || {},
        shippingAddress: data.shipping_address || {},
      };

      // Normalize field names if needed
      if (
        customer.value.billingAddress.zip_code &&
        !customer.value.billingAddress.zipcode
      ) {
        customer.value.billingAddress.zipcode = customer.value.billingAddress.zip_code;
      }

      if (
        customer.value.shippingAddress.zip_code &&
        !customer.value.shippingAddress.zipcode
      ) {
        customer.value.shippingAddress.zipcode = customer.value.shippingAddress.zip_code;
      }

      title.value = `Update customer: "${customer.value.first_name} ${customer.value.last_name}"`;

      // Load states for the customer's countries
      if (customer.value.billingAddress?.country_code) {
        countryStore.getStates(customer.value.billingAddress.country_code);
      }
      if (customer.value.shippingAddress?.country_code) {
        countryStore.getStates(customer.value.shippingAddress.country_code);
      }
    } catch (err) {
      console.error("Error fetching customer:", err);
    } finally {
      isLoading.value = false;
    }
  } else {
    title.value = "Create new customer";
  }
});
</script>

<style scoped></style>
