<template>
    <Head title="Contact Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Contact Admin') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <label class="block mb-2 text-sm font-medium text-gray-900" for="subject">
                                    {{ $t('Subject') }}
                                </label>
                                <input v-model="form.subject" id="subject" type="text" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    :placeholder="$t('Enter subject')">
                                <p v-if="form.errors.subject" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.subject }}
                                </p>
                            </div>

                            <div class="mb-6">
                                <label class="block mb-2 text-sm font-medium text-gray-900" for="message">
                                    {{ $t('Message') }}
                                </label>
                                <textarea v-model="form.message" id="message" rows="6" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    :placeholder="$t('Enter your message')"></textarea>
                                <p v-if="form.errors.message" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.message }}
                                </p>
                            </div>

                            <div class="flex items-center space-x-4">
                                <button type="submit"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center"
                                    :disabled="form.processing">
                                    {{ $t('Send Message') }}
                                </button>

                                <Link :href="route('vendor.contacts.index')"
                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                                {{ $t('Cancel') }}
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    subject: '',
    message: '',
});

const submit = () => {
    form.post(route('vendor.contacts.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>