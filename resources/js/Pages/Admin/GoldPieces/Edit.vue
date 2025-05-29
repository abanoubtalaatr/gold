<template>
    <Head :title="$t('Edit Gold Piece')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Edit Gold Piece') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Name -->
                                <div>
                                    <InputLabel for="name" :value="$t('Name')" />
                                    <TextInput id="name" v-model="form.name" type="text" class="block w-full mt-1"
                                        required autofocus />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <!-- Description -->
                                <div>
                                    <InputLabel for="description" :value="$t('Description')" />
                                    <TextArea id="description" v-model="form.description" class="block w-full mt-1" />
                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>

                                <!-- Weight -->
                                <div>
                                    <InputLabel for="weight" :value="$t('Weight (grams)')" />
                                    <TextInput id="weight" v-model="form.weight" type="number" step="0.01"
                                        class="block w-full mt-1" required />
                                    <InputError class="mt-2" :message="form.errors.weight" />
                                </div>

                                <!-- Carat -->
                                <div>
                                    <InputLabel for="carat" :value="$t('Carat')" />
                                    <select id="carat" v-model="form.carat"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                        required>
                                        <option value="18">18K</option>
                                        <option value="21">21K</option>
                                        <option value="22">22K</option>
                                        <option value="24">24K</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.carat" />
                                </div>

                                <!-- Type -->
                                <div>
                                    <InputLabel for="type" :value="$t('Type')" />
                                    <div class="relative">
                                        <select id="type" v-model="form.type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                            <option value="for_rent">{{ $t('For Rent') }}</option>
                                            <option value="for_sale">{{ $t('For Sale') }}</option>
                                        </select>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.type" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <InputLabel for="status" :value="$t('Status')" />
                                    <div class="relative">
                                        <select id="status" v-model="form.status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                            <option v-if="form.type === 'for_rent'" value="pending">{{ $t('Pending') }}</option>
                                            <option v-if="form.type === 'for_rent'" value="accepted">{{ $t('Accepted') }}</option>
                                            <option v-if="form.type === 'for_rent'" value="rented">{{ $t('Rented') }}</option>
                                            <option v-if="form.type === 'for_rent'" value="available">{{ $t('Available') }}</option>
                                            <option v-if="form.type === 'for_sale'" value="pending">{{ $t('Pending') }}</option>
                                            <option v-if="form.type === 'for_sale'" value="accepted">{{ $t('Accepted') }}</option>
                                            <option value="sold">{{ $t('Sold') }}</option>
                                        </select>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.status" />
                                </div>

                                <!-- Including Lobes Checkbox -->
                                <div class="flex items-center">
                                    <input id="is_including_lobes" v-model="form.is_including_lobes" type="checkbox"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                    <label for="is_including_lobes" class="block ml-2 text-sm text-gray-900">
                                        {{ $t('Including Lobes') }}
                                    </label>
                                </div>

                                <!-- Rental Price -->
                                <div v-if="form.type === 'for_rent'">
                                    <InputLabel for="rental_price_per_day" :value="$t('Rental Price Per Day')" />
                                    <TextInput id="rental_price_per_day" v-model="form.rental_price_per_day"
                                        type="number" step="0.01" class="block w-full mt-1" />
                                    <InputError class="mt-2" :message="form.errors.rental_price_per_day" />
                                </div>

                                <!-- Sale Price -->
                                <div v-if="form.type === 'for_sale'">
                                    <InputLabel for="sale_price" :value="$t('Sale Price')" />
                                    <TextInput id="sale_price" v-model="form.sale_price" type="number" step="0.01"
                                        class="block w-full mt-1" />
                                    <InputError class="mt-2" :message="form.errors.sale_price" />
                                </div>

                                <!-- Deposit Amount -->
                                <div>
                                    <InputLabel for="deposit_amount" :value="$t('Deposit Amount')" />
                                    <TextInput id="deposit_amount" v-model="form.deposit_amount" type="number"
                                        step="0.01" class="block w-full mt-1" />
                                    <InputError class="mt-2" :message="form.errors.deposit_amount" />
                                </div>

                                <!-- Images -->
                                <div class="md:col-span-2">
                                    <InputLabel :value="$t('Images')" />
                                    <div class="grid grid-cols-2 gap-4 mt-4 sm:grid-cols-3 md:grid-cols-4">
                                        <!-- Existing images -->
                                        <div v-for="image in goldPiece.images" :key="image.id"
                                            class="relative group aspect-square">
                                            <template v-if="!form.deleted_images.includes(image.id)">
                                                <img :src="image.url" class="object-cover w-full h-full rounded-lg" />
                                                <button type="button" @click="deleteImage(image.id)"
                                                    class="absolute top-0 right-0 p-1 text-white bg-red-500 rounded-full hover:opacity-100"
                                                    >
                                                    <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </template>
                                            <div v-else class="flex items-center justify-center w-full h-full bg-gray-100 rounded-lg">
                                                <button type="button" @click="restoreImage(image.id)"
                                                    class="p-2 text-indigo-600 bg-white rounded-full shadow">
                                                    <ArrowPathIcon class="w-5 h-5" />
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Newly uploaded images preview -->
                                        <div v-for="(preview, index) in previewImages" :key="'preview-' + index"
                                            class="relative group aspect-square">
                                            <img :src="preview.url" class="object-cover w-full h-full rounded-lg" />
                                            <button type="button" @click="removePreview(index)"
                                                class="absolute top-0 right-0 p-1 text-white bg-red-500 rounded-full">
                                                <TrashIcon class="w-4 h-4" />
                                            </button>
                                        </div>

                                        <!-- Upload button -->
                                        <label for="images"
                                            class="flex flex-col items-center justify-center border-2 border-gray-300 border-dashed rounded-lg cursor-pointer aspect-square hover:bg-gray-50">
                                            <div class="flex flex-col items-center justify-center p-4 text-center">
                                                <PlusIcon class="w-8 h-8 text-gray-400" />
                                                <p class="mt-2 text-sm text-gray-600">
                                                    {{ $t('Upload Images') }}
                                                </p>
                                            </div>
                                            <input id="images" type="file" multiple class="hidden"
                                                @change="handleImages" accept="image/*" />
                                        </label>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.images" />
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <SecondaryButton type="button" @click="cancel">
                                    {{ $t('Cancel') }}
                                </SecondaryButton>
                                <PrimaryButton type="submit" class="ml-3" :disabled="form.processing">
                                    {{ $t('Save Changes') }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import { PlusIcon, TrashIcon, ArrowPathIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    goldPiece: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.goldPiece.name,
    description: props.goldPiece.description,
    weight: props.goldPiece.weight,
    carat: props.goldPiece.carat,
    type: props.goldPiece.type,
    status: props.goldPiece.status,
    rental_price_per_day: props.goldPiece.rental_price_per_day,
    sale_price: props.goldPiece.sale_price,
    deposit_amount: props.goldPiece.deposit_amount,
    is_including_lobes: props.goldPiece.is_including_lobes || false,
    images: [],
    deleted_images: [],
});

const previewImages = ref([]);
const selectedFiles = ref([]);

// Watch for type changes to reset status if needed
watch(() => form.type, (newType) => {
    if (newType === 'for_rent' && !['pending', 'accepted', 'rented', 'available', 'sold'].includes(form.status)) {
        form.status = 'pending';
    } else if (newType === 'for_sale' && !['pending', 'accepted', 'sold'].includes(form.status)) {
        form.status = 'pending';
    }
}, { immediate: true });

const handleImages = (event) => {
    const files = Array.from(event.target.files);
    if (files.length) {
        // Add new files to selectedFiles
        selectedFiles.value = [...selectedFiles.value, ...files];

        // Update form.images with all selected files
        form.images = selectedFiles.value;

        // Generate previews for new files
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImages.value.push({
                    url: e.target.result,
                    file: file
                });
            };
            reader.readAsDataURL(file);
        });
    }

    // Reset the input to allow selecting the same files again
    event.target.value = '';
};

const deleteImage = (imageId) => {
    if (!form.deleted_images.includes(imageId)) {
        form.deleted_images = [...form.deleted_images, imageId];
    }
};

const restoreImage = (imageId) => {
    form.deleted_images = form.deleted_images.filter(id => id !== imageId);
};

const removePreview = (index) => {
    // Remove from previews
    const removed = previewImages.value.splice(index, 1);

    // Remove from selected files
    selectedFiles.value = selectedFiles.value.filter(file => file !== removed[0].file);
    form.images = selectedFiles.value;
};

const submit = () => {
    // Prepare form data for file upload
    const formData = new FormData();

    // Append all form fields
    Object.keys(form.data()).forEach(key => {
        if (key === 'images') {
            // Append each file individually
            form.images.forEach(file => {
                formData.append('images[]', file);
            });
        } else if (key === 'deleted_images') {
            // Append each deleted image ID individually
            form.deleted_images.forEach(id => {
                formData.append('deleted_images[]', id);
            });
        } else if (form[key] !== null && form[key] !== undefined) {
            formData.append(key, form[key]);
        }
    });

    form.post(route('admin.gold-pieces.update', props.goldPiece.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const cancel = () => {
    router.visit(route('admin.gold-pieces.index'));
};
</script>
