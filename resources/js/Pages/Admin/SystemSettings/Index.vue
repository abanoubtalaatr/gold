<template>

    <Head title="System Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                {{ $t('System Settings') }}
            </h2>
        </template>

        <div class="py-6 bg-gradient-to-b from-gray-50 to-white min-h-screen">
            <div class="mx-auto sm:px-3 lg:px-4">
                <!-- Success Message -->


                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $t('General Settings') }}</h3>

                        <form @submit.prevent="submitSettings" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <!-- Commission Settings -->
                            <div class="col-span-1 md:col-span-12">
                                <h4 class="text-md font-medium text-gray-700 mb-2 border-b pb-1">{{ $t('Commission Settings') }}
                                </h4>
                            </div>

                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="platform_commission_percentage"
                                    :value="$t('Platform Commission Percentage')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="platform_commission_percentage"
                                    v-model="settingsForm.platform_commission_percentage" type="number" step="0.01"
                                    min="0" max="100"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.platform_commission_percentage }" />
                                <InputError :message="settingsForm.errors.platform_commission_percentage"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="merchant_commission_percentage"
                                    :value="$t('Merchant Commission Percentage')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="merchant_commission_percentage"
                                    v-model="settingsForm.merchant_commission_percentage" type="number" step="0.01"
                                    min="0" max="100"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.merchant_commission_percentage }" />
                                <InputError :message="settingsForm.errors.merchant_commission_percentage"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- Tax Settings -->
                            <div class="col-span-1 md:col-span-12 mt-4">
                                <h4 class="text-md font-medium text-gray-700 mb-2 border-b pb-1">{{ $t('Tax Settings')
                                    }}</h4>
                            </div>

                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="tax_percentage" :value="$t('Tax Percentage')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="tax_percentage" v-model="settingsForm.tax_percentage" type="number"
                                    step="0.01" min="0" max="100"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.tax_percentage }" />
                                <InputError :message="settingsForm.errors.tax_percentage"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- Gold Settings -->
                            <div class="col-span-1 md:col-span-12 mt-4">
                                <h4 class="text-md font-medium text-gray-700 mb-2 border-b pb-1">{{ $t('Gold Settings')
                                    }}</h4>
                            </div>

                            <div class="col-span-1 md:col-span-4">
                                <InputLabel for="gold_purchase_price" :value="$t('Gold Purchase Price')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="gold_purchase_price" v-model="settingsForm.gold_purchase_price"
                                    type="number" step="0.01" min="0"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.gold_purchase_price }" />
                                <InputError :message="settingsForm.errors.gold_purchase_price"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <div class="col-span-1 md:col-span-4">
                                <InputLabel for="gold_purchase_additional_percentage"
                                    :value="$t('Purchase Additional Percentage')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="gold_purchase_additional_percentage"
                                    v-model="settingsForm.gold_purchase_additional_percentage" type="number" step="0.01"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.gold_purchase_additional_percentage }" />
                                <InputError :message="settingsForm.errors.gold_purchase_additional_percentage"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <div class="col-span-1 md:col-span-4">
                                <InputLabel for="gold_rental_price_percentage"
                                    :value="$t('Gold Rental Price Percentage')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="gold_rental_price_percentage"
                                    v-model="settingsForm.gold_rental_price_percentage" type="number" step="0.01"
                                    min="0"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.gold_rental_price_percentage }" />
                                <InputError :message="settingsForm.errors.gold_rental_price_percentage"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <div class="col-span-1 md:col-span-4">
                                <InputLabel for="gold_rental_insurance_percentage"
                                    :value="$t('Rental Insurance Percentage')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="gold_rental_insurance_percentage"
                                    v-model="settingsForm.gold_rental_insurance_percentage" type="number" step="0.01"
                                    min="0"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.gold_rental_insurance_percentage }" />
                                <InputError :message="settingsForm.errors.gold_rental_insurance_percentage"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- Order Settings -->
                            <div class="col-span-1 md:col-span-12 mt-4">
                                <h4 class="text-md font-medium text-gray-700 mb-2 border-b pb-1">{{ $t('Order Settings')
                                    }}</h4>
                            </div>

                            <div class="col-span-1 md:col-span-4">
                                <InputLabel for="booking_insurance_amount" :value="$t('Booking Insurance Amount')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="booking_insurance_amount" v-model="settingsForm.booking_insurance_amount"
                                    type="number" step="0.01" min="0"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.booking_insurance_amount }" />
                                <InputError :message="settingsForm.errors.booking_insurance_amount"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <div class="col-span-1 md:col-span-4">
                                <InputLabel for="minimum_payout_amount" :value="$t('Minimum Payout Amount (SAR)')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="minimum_payout_amount" v-model="settingsForm.minimum_payout_amount"
                                    type="number" step="0.01" min="0"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.minimum_payout_amount }" />
                                <InputError :message="settingsForm.errors.minimum_payout_amount"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <div class="col-span-1 md:col-span-4">
                                <InputLabel for="max_delivery_time_hours" :value="$t('Max Delivery Time (hours)')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="max_delivery_time_hours" v-model="settingsForm.max_delivery_time_hours"
                                    type="number" step="1" min="1"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.max_delivery_time_hours }" />
                                <InputError :message="settingsForm.errors.max_delivery_time_hours"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- Website Content -->
                            <div class="col-span-1 md:col-span-12 mt-4">
                                <h4 class="text-md font-medium text-gray-700 mb-2 border-b pb-1">{{ $t('Website Content') }}
                                </h4>
                            </div>

                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="privacy_policy" :value="$t('Privacy Policy')"
                                    class="text-sm font-semibold text-gray-800" />
                                <textarea id="privacy_policy" v-model="settingsForm.privacy_policy" rows="5"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.privacy_policy }"></textarea>
                                <InputError :message="settingsForm.errors.privacy_policy"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="terms_conditions" :value="$t('Terms & Conditions')"
                                    class="text-sm font-semibold text-gray-800" />
                                <textarea id="terms_conditions" v-model="settingsForm.terms_conditions" rows="5"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.terms_conditions }"></textarea>
                                <InputError :message="settingsForm.errors.terms_conditions"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- Contact Information -->
                            <div class="col-span-1 md:col-span-12 mt-4">
                                <h4 class="text-md font-medium text-gray-700 mb-2 border-b pb-1">{{ $t('Contact Information') }}
                                </h4>
                            </div>

                            <div class="col-span-1 md:col-span-12">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <InputLabel for="contact_phone" :value="$t('Contact Phone')"
                                            class="text-sm font-semibold text-gray-800" />
                                        <TextInput id="contact_phone" v-model="settingsForm.contact_phone" type="text"
                                            class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                            :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.contact_phone }" />
                                        <InputError :message="settingsForm.errors.contact_phone"
                                            class="mt-1 text-xs text-red-500 font-medium" />
                                    </div>

                                    <div>
                                        <InputLabel for="contact_email" :value="$t('Contact Email')"
                                            class="text-sm font-semibold text-gray-800" />
                                        <TextInput id="contact_email" v-model="settingsForm.contact_email" type="email"
                                            class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                            :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.contact_email }" />
                                        <InputError :message="settingsForm.errors.contact_email"
                                            class="mt-1 text-xs text-red-500 font-medium" />
                                    </div>

                                    <div>
                                        <InputLabel for="location_map" :value="$t('Location Map URL')"
                                            class="text-sm font-semibold text-gray-800" />
                                        <TextInput id="location_map" v-model="settingsForm.location_map" type="text"
                                            class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                            :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.location_map }" />
                                        <InputError :message="settingsForm.errors.location_map"
                                            class="mt-1 text-xs text-red-500 font-medium" />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-span-1 md:col-span-12 flex items-center justify-end mt-6">
                                <PrimaryButton
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold text-sm rounded-md hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200"
                                    :disabled="settingsForm.processing">
                                    {{ $t('Save Settings') }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Home Sliders Section -->
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $t('Home Sliders') }}</h3>
                            <button @click="showSliderForm = true"
                                class="inline-flex items-center px-3 py-1.5 bg-ingradient from-green-600 to-green-700 text-black font-semibold text-xs rounded-md hover:from-green-700 hover:to-green-800 transition-all duration-200">
                                {{ $t('Add New Slider') }}
                            </button>
                        </div>

                        <!-- Slider Form (shown when adding/editing) -->
                        <div v-if="showSliderForm" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="text-md font-medium text-gray-700 mb-3">
                                {{ editingSlider ? $t('Edit Slider') : $t('Add New Slider') }}
                            </h4>

                            <form @submit.prevent="submitSlider" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <div class="col-span-1 md:col-span-12">
                                    <InputLabel for="image" :value="$t('Slider Image')"
                                        class="text-sm font-semibold text-gray-800" />
                                    <div class="relative mt-1">
                                        <input id="image" type="file" @input="sliderForm.image = $event.target.files[0]"
                                            accept="image/*" class="block w-full text-sm text-gray-500
             file:mr-4 file:py-2 file:px-4
             file:rounded-md file:border-0
             file:text-sm file:font-semibold
             file:bg-indigo-50 file:text-indigo-700
             hover:file:bg-indigo-100
             focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2
             rounded-md border-2 border-gray-200 bg-gray-50"
                                            :class="{ 'border-red-500 focus:border-red-500': sliderForm.errors.image }" />
                                        <InputError :message="sliderForm.errors.image"
                                            class="mt-1 text-xs text-red-500 font-medium" />
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $t('Recommended size: 1200x500px, Max size: 5MB') }}
                                        </p>

                                        <!-- Image Preview -->
                                        <div v-if="sliderForm.image || (editingSlider && !sliderForm.image)"
                                            class="mt-4">
                                            <div class="relative group">
                                                <img :src="imagePreviewUrl || (editingSlider ? '/storage/' + editingSlider.image_path : '')"
                                                    class="w-full max-w-md h-48 object-cover rounded-lg border-2 border-gray-200 shadow-sm transition-all duration-200 group-hover:shadow-md"
                                                    alt="Slider Image Preview" />
                                                <!-- Overlay for hover effect -->
                                                <div
                                                    class="absolute inset-0 bg-gray-900 bg-opacity-0 group-hover:bg-opacity-20 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                                    <span
                                                        class="text-white text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                        {{ $t('Preview') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-1 md:col-span-6">
                                    <InputLabel for="display_from" :value="$t('Display From')"
                                        class="text-sm font-semibold text-gray-800" />
                                    <TextInput id="display_from" v-model="sliderForm.display_from" type="date"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                        :class="{ 'border-red-500 focus:border-red-500': sliderForm.errors.display_from }" />
                                    <InputError :message="sliderForm.errors.display_from"
                                        class="mt-1 text-xs text-red-500 font-medium" />
                                </div>

                                <div class="col-span-1 md:col-span-6">
                                    <InputLabel for="display_to" :value="$t('Display To')"
                                        class="text-sm font-semibold text-gray-800" />
                                    <TextInput id="display_to" v-model="sliderForm.display_to" type="date"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                        :class="{ 'border-red-500 focus:border-red-500': sliderForm.errors.display_to }" />
                                    <InputError :message="sliderForm.errors.display_to"
                                        class="mt-1 text-xs text-red-500 font-medium" />
                                </div>

                                <div class="col-span-1 md:col-span-6">
                                    <InputLabel for="display_order" :value="$t('Display Order')"
                                        class="text-sm font-semibold text-gray-800" />
                                    <TextInput id="display_order" v-model="sliderForm.display_order" type="number"
                                        min="1"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                        :class="{ 'border-red-500 focus:border-red-500': sliderForm.errors.display_order }" />
                                    <InputError :message="sliderForm.errors.display_order"
                                        class="mt-1 text-xs text-red-500 font-medium" />
                                </div>

                                <div class="col-span-1 md:col-span-6">
                                    <InputLabel for="is_active" :value="$t('Status')"
                                        class="text-sm font-semibold text-gray-800" />
                                    <select id="is_active" v-model="sliderForm.is_active"
                                        class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                        :class="{ 'border-red-500 focus:border-red-500': sliderForm.errors.is_active }">
                                        <option value="1">{{ $t('Active') }}</option>
                                        <option value="0">{{ $t('Inactive') }}</option>
                                    </select>
                                    <InputError :message="sliderForm.errors.is_active"
                                        class="mt-1 text-xs text-red-500 font-medium" />
                                </div>

                                <div class="col-span-1 md:col-span-12 flex items-center justify-end space-x-2 mt-3">
                                    <button type="button" @click="cancelSliderForm"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 hover:text-gray-900 transition-all duration-200">
                                        {{ $t('Cancel') }}
                                    </button>
                                    <PrimaryButton
                                        class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold text-xs rounded-md hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200"
                                        :disabled="sliderForm.processing">
                                        {{ editingSlider ? $t('Update Slider') : $t('Add Slider') }}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>

                        <!-- Sliders Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Image') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Display Period') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Order') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Status') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="slider in sliders" :key="slider.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <img :src="'/storage/' + slider.image_path"
                                                class="h-12 w-20 object-cover rounded">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(slider.display_from) }} - {{ formatDate(slider.display_to) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ slider.display_order }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="slider.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ slider.is_active ? $t('Active') : $t('Inactive') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button @click="editSlider(slider)"
                                                    class="inline-flex items-center px-3 py-1 bg-indigo-100 border border-transparent rounded-md text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-xs">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    {{ $t('Edit') }}
                                                </button>
                                                <button @click="deleteSlider(slider)"
                                                    class="inline-flex items-center px-3 py-1 bg-red-100 border border-transparent rounded-md text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-xs">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    {{ $t('Delete') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="sliders.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            {{ $t('No sliders found') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    settings: Object,
    sliders: Array,
});

// Initialize refs
const showSliderForm = ref(false);
const editingSlider = ref(null);
const sliders = ref(props.sliders || []);
const imagePreviewUrl = ref(null);

// Initialize forms
const settingsForm = useForm({
    platform_commission_percentage: props.settings?.platform_commission_percentage || 0,
    merchant_commission_percentage: props.settings?.merchant_commission_percentage || 0,
    tax_percentage: props.settings?.tax_percentage || 0,
    gold_purchase_price: props.settings?.gold_purchase_price || 0,
    gold_purchase_additional_percentage: props.settings?.gold_purchase_additional_percentage || 0,
    gold_rental_price_percentage: props.settings?.gold_rental_price_percentage || 0,
    gold_rental_insurance_percentage: props.settings?.gold_rental_insurance_percentage || 0,
    booking_insurance_amount: props.settings?.booking_insurance_amount || 0,
    minimum_payout_amount: props.settings?.minimum_payout_amount || 0,
    max_delivery_time_hours: props.settings?.max_delivery_time_hours || 24,
    privacy_policy: props.settings?.privacy_policy || '',
    terms_conditions: props.settings?.terms_conditions || '',
    contact_phone: props.settings?.contact_phone || '',
    contact_email: props.settings?.contact_email || '',
    location_map: props.settings?.location_map || '',
});

const sliderForm = useForm({
    image: null,
    display_from: '',
    display_to: '',
    display_order: 1,
    is_active: true,
    _method: '', // Add _method for PUT requests
});

// Watch for image changes to generate preview
watch(
    () => sliderForm.image,
    (newImage) => {
        if (newImage) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreviewUrl.value = e.target.result;
            };
            reader.onerror = () => {
                console.error('Error reading image file');
                imagePreviewUrl.value = null;
            };
            reader.readAsDataURL(newImage);
        } else {
            imagePreviewUrl.value = null;
        }
    }
);

const submitSettings = () => {
    settingsForm.put(route('system-settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Success!',
                text: 'Settings updated successfully',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        },
        onError: (errors) => {
            console.error('Error updating settings:', errors);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to update settings',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        },
    });
};

const editSlider = (slider) => {
    try {
        editingSlider.value = slider;
        sliderForm.reset();
        sliderForm.display_from = slider.display_from?.split(' ')[0] || '';
        sliderForm.display_to = slider.display_to?.split(' ')[0] || '';
        sliderForm.display_order = slider.display_order || 1;
        sliderForm.is_active = slider.is_active ?? true;
        sliderForm._method = 'PUT'; // Explicitly set for PUT request
        imagePreviewUrl.value = slider.image_path ? `/storage/${slider.image_path}` : null;
        showSliderForm.value = true;
    } catch (error) {
        console.error('Error editing slider:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to edit slider',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const cancelSliderForm = () => {
    showSliderForm.value = false;
    editingSlider.value = null;
    sliderForm.reset();
    sliderForm.clearErrors();
    imagePreviewUrl.value = null;
};

const submitSlider = () => {
    try {
        // Create FormData object
        const formData = new FormData();

        // Append all form fields
        if (sliderForm.image) {
            formData.append('image', sliderForm.image);
        }
        formData.append('display_from', sliderForm.display_from);
        formData.append('display_to', sliderForm.display_to);
        formData.append('display_order', sliderForm.display_order);
        formData.append('is_active', sliderForm.is_active);

        const requestOptions = {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Success!',
                    text: editingSlider.value ? 'Slider updated successfully' : 'Slider created successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            },
            onError: (errors) => {
                console.error('Error submitting slider:', errors);
                Swal.fire({
                    title: 'Error!',
                    text: editingSlider.value ? 'Failed to update slider' : 'Failed to create slider',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
        };

        if (editingSlider.value) {
            formData.append('_method', 'PUT'); // Laravel's way to simulate PUT request
            router.post(route('system-settings.sliders.update', editingSlider.value.id), formData, requestOptions);
        } else {
            router.post(route('system-settings.sliders.store'), formData, requestOptions);
        }
    } catch (error) {
        console.error('Error submitting slider:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};
const confirmDeleteSlider = (slider) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteSlider(slider);
        }
    });
};

const deleteSlider = (slider) => {
    try {
        router.delete(route('system-settings.sliders.destroy', slider.id), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Slider has been deleted.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            },
            onError: () => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete slider',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
        });
    } catch (error) {
        console.error('Error deleting slider:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const formatDate = (dateString) => {
    try {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString();
    } catch (error) {
        console.error('Error formatting date:', error);
        return '';
    }
};
</script>
