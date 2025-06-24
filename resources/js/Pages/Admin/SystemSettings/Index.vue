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
                                <InputLabel for="gold_purchase_price" :value="$t('Gold Sell Price')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="gold_purchase_price" v-model="settingsForm.gold_purchase_price"
                                    type="number" step="0.01" min="0"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.gold_purchase_price }" />
                                <InputError :message="settingsForm.errors.gold_purchase_price"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <div class="col-span-1 md:col-span-4">
                                <InputLabel for="max_canceled_orders" :value="$t('Maximum Canceled Orders')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="max_canceled_orders" v-model="settingsForm.max_canceled_orders"
                                    type="number" step="0.01" min="0"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.max_canceled_orders }" />
                                <InputError :message="settingsForm.errors.max_canceled_orders"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <div class="col-span-1 md:col-span-4">
                                <InputLabel for="max_active_orders" :value="$t('Maximum Active Orders')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="max_active_orders" v-model="settingsForm.max_active_orders"
                                    type="number" step="0.01" min="0"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.max_active_orders }" />
                                <InputError :message="settingsForm.errors.max_active_orders"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <div class="col-span-1 md:col-span-4">
                                <InputLabel for="vendor_debt_limit" :value="$t('Vendor Debt Limit')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="vendor_debt_limit" v-model="settingsForm.vendor_debt_limit"
                                    type="number" step="0.01" min="0"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': settingsForm.errors.vendor_debt_limit }" />
                                <InputError :message="settingsForm.errors.vendor_debt_limit"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>
<!-- 
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
                            </div> -->

                            <!-- <div class="col-span-1 md:col-span-4">
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
                            </div> -->

                            <!-- <div class="col-span-1 md:col-span-4">
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
                            </div> -->

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
    max_canceled_orders: props.settings?.max_canceled_orders || 0,
    vendor_debt_limit: props.settings?.vendor_debt_limit || 0,
    max_active_orders: props.settings?.max_active_orders || 0,
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
