<x-app-layout>
    <x-show-message />
    <div class="container mx-auto mt-20 lg:w-2/3 xl:w-2/3">
        <div>
            <div class="grid items-start grid-cols-1 gap-6 sm:grid-cols-5">
                <div class="col-span-3 p-4 bg-white rounded-lg shadow">
                    <form x-data="{
                        countries: {{ json_encode($countries) }},
                        billingAddress: {{ json_encode([
                            'address1' => old('billing.address1', $billingAddress->address1),
                            'address2' => old('billing.address2', $billingAddress->address2),
                            'city' => old('billing.city', $billingAddress->city),
                            'state' => old('billing.state', $billingAddress->state),
                            'country_code' => old('billing.country_code', $billingAddress->country_code),
                            'zip_code' => old('billing.zip_code', $billingAddress->zip_code),
                        ]) }},
                        shippingAddress: {{ json_encode([
                            'address1' => old('shipping.address1', $shippingAddress->address1),
                            'address2' => old('shipping.address2', $shippingAddress->address2),
                            'city' => old('shipping.city', $shippingAddress->city),
                            'state' => old('shipping.state', $shippingAddress->state),
                            'country_code' => old('shipping.country_code', $shippingAddress->country_code),
                            'zip_code' => old('shipping.zip_code', $shippingAddress->zip_code),
                        ]) }},
                        get billingCountryStates() {
                            const country = this.countries.find(country => country.code === this.billingAddress.country_code);
                            if (country && country.states) {
                                return JSON.parse(country.states);
                            }
                            return null;
                        },
                        get shippingCountryStates() {
                            const country = this.countries.find(country => country.code === this.shippingAddress.country_code);
                            if (country && country.states) {
                                return JSON.parse(country.states);
                            }
                            return null;
                        },
                    }" action="{{ route('profile.update.customer') }}" method="POST">
                        @csrf
                        <h2 class="mb-5 text-xl font-semibold">Your Profile</h2>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <x-input type="text" name="first_name" placeholder="First Name"
                                value="{{ old('first_name', $customer->first_name) }}"
                                class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />

                            <x-input type="text" name="last_name" placeholder="Last Name"
                                value="{{ old('last_name', $customer->last_name) }}"
                                class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                        </div>
                        <div class="mb-4">
                            <x-input placeholder="Your Email" type="email" name="email"
                                value="{{ old('email', $user->email) }}"
                                class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                        </div>
                        <div class="mb-4">
                            <x-input placeholder="Your Phone" type="text" name="phone"
                                value="{{ old('phone', $customer->phone) }}"
                                class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                        </div>
                        <h2 class="mb-5 text-xl font-semibold">Billing Address</h2>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="flex-1 mb-4">
                                <x-input placeholder="Address 1" type="text" name="billing[address1]"
                                    x-model="billingAddress.address1"
                                    x-on:change="billingAddress.address1 = $event.target.value"
                                    class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                            </div>
                            <div class="flex-1 mb-4">
                                <x-input placeholder="Address 2" type="text" name="billing[address2]"
                                    x-model="billingAddress.address2"
                                    x-on:change="billingAddress.address2 = $event.target.value"
                                    class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="flex-1 mb-4">
                                <x-input placeholder="City" type="text" name="billing[city]"
                                    x-model="billingAddress.city"
                                    class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                            </div>
                            <div class="flex-1 mb-4">
                                <input placeholder="Zipcode" type="text" name="billing[zip_code]"
                                    x-model="billingAddress.zip_code"
                                    class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="flex-1 mb-4">
                                <select placeholder="Country" name="billing[country_code]"
                                    x-model="billingAddress.country_code"
                                    class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500">
                                    <option value="">Select Country</option>
                                    <template x-for="country in countries" :key="country.code">
                                        <option :selected="country.code === billingAddress.country_code"
                                            x-text="country.name" :value="country.code"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex-1 mb-4">
                                <template x-if="billingCountryStates">
                                    <x-input type="select" name="billing[state]" x-model="billingAddress.state"
                                        class="w-full border-gray-300 rounded focus:border-purple-600 focus:ring-purple-600">
                                        <option value="">Select State</option>
                                        <template x-for="[code, state] of Object.entries(billingCountryStates)"
                                            :key="code">
                                            <option :selected="code === billingAddress.state" :value="code"
                                                x-text="state"></option>
                                        </template>
                                    </x-input>
                                </template>
                                <template x-if="!billingCountryStates">
                                    <x-input type="text" name="billing[state]" x-model="billingAddress.state"
                                        placeholder="State"
                                        class="w-full border-gray-300 rounded focus:border-purple-600 focus:ring-purple-600" />
                                </template>
                            </div>
                        </div>

                        <div class="flex justify-between mt-6">
                            <h2 class="mb-5 text-xl font-semibold">Shipping Address</h2>
                            <label for="sameAsBillingAddress" class="text-gray-700">
                                <input type="checkbox" id="sameAsBillingAddress" name="same_as_billing"
                                    @change="$event.target.checked ? shippingAddress = {...billingAddress} : ''" />
                                <span class="ml-2">Same as billing address</span>
                            </label>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="flex-1 mb-4">
                                <x-input placeholder="Address 1" type="text" name="shipping[address1]"
                                    x-model="shippingAddress.address1"
                                    x-on:change="shippingAddress.address1 = $event.target.value"
                                    class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                            </div>
                            <div class="flex-1 mb-4">
                                <x-input placeholder="Address 2" type="text" name="shipping[address2]"
                                    x-model="shippingAddress.address2"
                                    x-on:change="shippingAddress.address2 = $event.target.value"
                                    class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="flex-1 mb-4">
                                <x-input placeholder="City" type="text" name="shipping[city]"
                                    x-model="shippingAddress.city"
                                    class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                            </div>
                            <div class="flex-1 mb-4">
                                <input placeholder="Zipcode" type="text" name="shipping[zip_code]"
                                    x-model="shippingAddress.zip_code"
                                    class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="flex-1 mb-4">
                                <select placeholder="Country" name="shipping[country_code]"
                                    x-model="shippingAddress.country_code"
                                    class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500">
                                    <option value="">Select Country</option>
                                    <template x-for="country in countries" :key="country.code">
                                        <option :selected="country.code === shippingAddress.country_code"
                                            x-text="country.name" :value="country.code"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex-1 mb-4">
                                <template x-if="shippingCountryStates">
                                    <x-input type="select" name="shipping[state]" x-model="shippingAddress.state"
                                        class="w-full border-gray-300 rounded focus:border-purple-600 focus:ring-purple-600">
                                        <option value="">Select State</option>
                                        <template x-for="[code, state] of Object.entries(shippingCountryStates)"
                                            :key="code">
                                            <option :selected="code === shippingAddress.state" :value="code"
                                                x-text="state"></option>
                                        </template>
                                    </x-input>
                                </template>
                                <template x-if="!shippingCountryStates">
                                    <x-input type="text" name="billing[state]" x-model="shippingAddress.state"
                                        placeholder="State"
                                        class="w-full border-gray-300 rounded focus:border-purple-600 focus:ring-purple-600" />
                                </template>
                            </div>
                        </div>
                        <button class="w-full btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700">
                            Update
                        </button>
                    </form>
                </div>
                <div class="col-span-2 p-4 bg-white rounded-lg shadow">
                    <h2 class="mb-5 text-xl">Update Password</h2>
                    <form method="POST" action="{{ route('profile.update.password') }}">
                        @csrf
                        <div class="mb-4">
                            <x-input type="password" name="current_password" placeholder="Your Current password"
                                class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                        </div>
                        <div class="mb-4">
                            <x-input type="password" name="new_password" placeholder="New password"
                                class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                        </div>
                        <div class="mb-4">
                            <x-input type="password" name="new_password_confirmation"
                                placeholder="Repeat new password"
                                class="w-full border-gray-300 rounded-md focus:border-purple-500 focus:outline-none focus:ring-purple-500" />
                        </div>
                        <div>
                            <x-primary-button
                                class="btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700">
                                {{ __('Reset Password') }}
                            </x-primary-button>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
