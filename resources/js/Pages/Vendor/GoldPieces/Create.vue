<template>
  <Head :title="editMode ? $t('Edit Gold Piece') : $t('Add New Gold Piece')" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">
        {{ editMode ? $t('Edit Gold Piece') : $t('Add New Gold Piece') }}
      </h2>
    </template>

    <div class="py-6 bg-gradient-to-b from-gray-50 to-white min-h-screen">
      <div class="mx-auto sm:px-3 lg:px-4">
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
          <div class="p-4">
            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-12 gap-4">
              <!-- Gold Piece Name -->
              <div class="col-span-1 md:col-span-6">
                <InputLabel
                  for="name"
                  :value="$t('Gold Piece Name')"
                  class="text-sm font-semibold text-gray-800"
                />
                <TextInput
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.name }"
                  :placeholder="$t('Enter gold piece name')"
                />
                <InputError
                  :message="form.errors.name"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- Weight -->
              <div class="col-span-1 md:col-span-6">
                <InputLabel
                  for="weight"
                  :value="$t('Weight (grams)')"
                  class="text-sm font-semibold text-gray-800"
                />
                <TextInput
                  id="weight"
                  v-model="form.weight"
                  type="number"
                  step="0.01"
                  min="0.1"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.weight }"
                />
                <InputError
                  :message="form.errors.weight"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- Carat -->
              <div class="col-span-1 md:col-span-6">
                <InputLabel
                  for="carat"
                  :value="$t('Carat')"
                  class="text-sm font-semibold text-gray-800"
                />
                <select
                  id="carat"
                  v-model="form.carat"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.carat }"
                >
                  <option value="" disabled selected>{{ $t('Select carat') }}</option>
                  <option value="18">18K</option>
                  <option value="21">21K</option>
                  <option value="24">24K</option>
                </select>
                <InputError
                  :message="form.errors.carat"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- Type -->
              <div class="col-span-1 md:col-span-6">
                <InputLabel
                  for="type"
                  :value="$t('Type')"
                  class="text-sm font-semibold text-gray-800"
                />
                <select
                  id="type"
                  v-model="form.type"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.type }"
                  @change="handleTypeChange"
                >
                  <option value="" disabled selected>{{ $t('Select type') }}</option>
                  <option value="for_rent">{{ $t('For Rent') }}</option>
                  <option value="for_sale">{{ $t('For Sale') }}</option>
                </select>
                <InputError
                  :message="form.errors.type"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- Rental Price (shown if type is for_rent) -->
              <div v-show="form.type === 'for_rent'" class="col-span-1 md:col-span-6">
                <InputLabel
                  for="rental_price_per_day"
                  :value="$t('Rental Price (per day)')"
                  class="text-sm font-semibold text-gray-800"
                />
                <TextInput
                  id="rental_price_per_day"
                  v-model="form.rental_price_per_day"
                  type="number"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.rental_price_per_day }"
                  :placeholder="$t('Enter rental price')"
                />
                <InputError
                  :message="form.errors.rental_price_per_day"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- Deposit Amount (shown if type is for_rent) -->
              <div v-show="form.type === 'for_rent'" class="col-span-1 md:col-span-6">
                <InputLabel
                  for="deposit_amount"
                  :value="$t('Deposit Amount')"
                  class="text-sm font-semibold text-gray-800"
                />
                <TextInput
                  id="deposit_amount"
                  v-model="form.deposit_amount"
                  type="number"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.deposit_amount }"
                  :placeholder="$t('Enter deposit amount')"
                />
                <InputError
                  :message="form.errors.deposit_amount"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- Sale Price (shown if type is for_sale) -->
              <div v-show="form.type === 'for_sale'" class="col-span-1 md:col-span-6">
                <InputLabel
                  for="sale_price"
                  :value="$t('Sale Price')"
                  class="text-sm font-semibold text-gray-800"
                />
                <TextInput
                  id="sale_price"
                  v-model="form.sale_price"
                  type="number"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.sale_price }"
                  :placeholder="$t('Enter sale price')"
                />
                <InputError
                  :message="form.errors.sale_price"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- Branch Selection -->
              <div class="col-span-1 md:col-span-6">
                <InputLabel
                  for="branch_id"
                  :value="$t('Branch')"
                  class="text-sm font-semibold text-gray-800"
                />
                <select
                  id="branch_id"
                  v-model="form.branch_id"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.branch_id }"
                >
                  <option value="" disabled selected>{{ $t('Select branch') }}</option>
                  <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                    {{ branch.name }}
                  </option>
                </select>
                <InputError
                  :message="form.errors.branch_id"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- Description -->
              <div class="col-span-1 md:col-span-6">
                <InputLabel
                  for="description"
                  :value="$t('Description')"
                  class="text-sm font-semibold text-gray-800"
                />
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.description }"
                  :placeholder="$t('Enter description')"
                ></textarea>
                <InputError
                  :message="form.errors.description"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- Gold Piece Images -->
              <div class="col-span-1 md:col-span-12">
                <InputLabel
                  :value="$t('Gold Piece Images')"
                  class="text-sm font-semibold text-gray-800"
                />
                <FileUpload
                  v-model="form.images"
                  :multiple="true"
                  accept="image/*"
                  :maxFiles="5"
                  :maxSize="5120"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50"
                  :class="{ 'border-red-500': form.errors.images }"
                />
                <p class="mt-1 text-xs text-gray-500 font-medium">
                  {{ $t('Max 5 images, 5MB each (JPG, PNG)') }}
                </p>
                <InputError
                  :message="form.errors.images"
                  class="mt-1 text-xs text-red-500 font-medium"
                />

                <!-- Display existing images in edit mode -->
                <div v-if="editMode && existingImages.length" class="mt-4">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $t('Existing Images') }}</h4>
                  <div class="flex flex-wrap gap-2">
                    <div v-for="(image, index) in existingImages" :key="index" class="relative">
                      <img :src="'/storage/' + image.path" class="h-20 w-20 object-cover rounded border">
                      <button
                        type="button"
                        @click="removeExistingImage(index)"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="col-span-1 md:col-span-12 flex items-center justify-end mt-3 space-x-2">
                <Link
                  :href="route('vendor.gold-pieces.index')"
                  class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 hover:text-gray-900 transition-all duration-200"
                >
                  {{ $t('Cancel') }}
                </Link>
                <PrimaryButton
                  class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold text-xs rounded-md hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200"
                  :disabled="form.processing"
                >
                  {{ editMode ? $t('Update Gold Piece') : $t('Create Gold Piece') }}
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
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FileUpload from '@/Components/FileUpload.vue';

const props = defineProps({
  branches: {
    type: Array,
    default: () => [],
  },
  goldPiece: {
    type: Object,
    default: null,
  },
});

const editMode = computed(() => !!props.goldPiece);

const form = useForm({
  name: props.goldPiece?.name || '',
  weight: props.goldPiece?.weight || '',
  carat: props.goldPiece?.carat || '',
  type: props.goldPiece?.type || '',
  description: props.goldPiece?.description || '',
  rental_price_per_day: props.goldPiece?.rental_price_per_day || '',
  sale_price: props.goldPiece?.sale_price || '',
  deposit_amount: props.goldPiece?.deposit_amount || '',
  branch_id: props.goldPiece?.branch_id || '',
  images: [],
  deleted_images: [],
});

const existingImages = ref(props.goldPiece?.images || []);

const handleTypeChange = () => {
  // Reset prices when type changes
  if (form.type === 'for_rent') {
    form.sale_price = '';
  } else if (form.type === 'for_sale') {
    form.rental_price_per_day = '';
    form.deposit_amount = '';
  }
};

const removeExistingImage = (index) => {
  const imageToRemove = existingImages.value[index];
  form.deleted_images.push(imageToRemove.id);
  existingImages.value.splice(index, 1);
};

const submit = () => {
  if (editMode.value) {
    form.put(route('vendor.gold-pieces.update', props.goldPiece.id), {
      _method: 'PUT',
      onSuccess: () => {
        // Handle success
      },
      onError: () => {
        // Handle error
      },
    });
  } else {
    form.post(route('vendor.gold-pieces.store'), {
      onSuccess: () => {
        // Handle success
      },
      onError: () => {
        // Handle error
      },
    });
  }
};
</script>
