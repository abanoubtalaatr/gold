<template>

    <Head :title="$t('Create Gold Piece')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Create Gold Piece') }}
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
                                    <SelectInput id="carat" v-model="form.carat" class="block w-full mt-1" required>
                                        <option value="18">18K</option>
                                        <option value="21">21K</option>
                                        <option value="22">22K</option>
                                        <option value="24">24K</option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.carat" />
                                </div>

                                <!-- Type -->
                                <div>
                                    <InputLabel for="type" :value="$t('Type')" />
                                    <SelectInput id="type" v-model="form.type" class="block w-full mt-1" required>
                                        <option value="for_rent">{{ $t('For Rent') }}</option>
                                        <option value="for_sale">{{ $t('For Sale') }}</option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.type" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <InputLabel for="status" :value="$t('Status')" />
                                    <SelectInput id="status" v-model="form.status" class="block w-full mt-1" required>
                                        <option value="pending">{{ $t('Pending') }}</option>
                                        <option value="available">{{ $t('Available') }}</option>
                                        <option value="rented">{{ $t('Rented') }}</option>
                                        <option value="sold">{{ $t('Sold') }}</option>
                                        <option value="unavailable">{{ $t('Unavailable') }}</option>
                                    </SelectInput>
                                    <InputError class="mt-2" :message="form.errors.status" />
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
                                        <div v-for="(image, index) in previewImages" :key="'preview-' + index"
                                            class="relative group aspect-square">
                                            <img :src="image.url" class="object-cover w-full h-full rounded-lg" />
                                            <button type="button" @click="removeImage(index)"
                                                class="absolute top-0 right-0 p-1 text-white bg-red-500 rounded-full opacity-0 group-hover:opacity-100">
                                                <TrashIcon class="w-4 h-4" />
                                            </button>
                                        </div>
                                        <label for="images"
                                            class="flex flex-col items-center justify-center border-2 border-gray-300 border-dashed rounded-lg cursor-pointer aspect-square hover:bg-gray-50">
                                            <div class="flex flex-col items-center justify-center p-4 text-center">
                                                <PlusIcon class="w-8 h-8 text-gray-400" />
                                                <p class="mt-2 text-sm text-gray-600">
                                                    {{ $t('Upload Images') }}
                                                </p>
                                            </div>
                                            <input id="images" type="file" multiple class="hidden"
                                                @change="handleImages" />
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
                                    {{ $t('Create') }}
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
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/Select.vue';
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    description: '',
    weight: '',
    carat: '18',
    type: 'for_rent',
    status: 'pending',
    rental_price_per_day: '',
    sale_price: '',
    deposit_amount: '',
    images: [],
});

const previewImages = ref([]);

const handleImages = (event) => {
    const files = event.target.files;
    if (files) {
        form.images = Array.from(files);
        previewImages.value = [];
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImages.value.push({ url: e.target.result });
            };
            reader.readAsDataURL(file);
        });
    }
};

const removeImage = (index) => {
    previewImages.value.splice(index, 1);
    form.images.splice(index, 1);
};

const submit = () => {
    form.post(route('admin.gold-pieces.store'), {
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
