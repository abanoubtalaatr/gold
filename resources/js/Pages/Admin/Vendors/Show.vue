<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Vendor Details') }}
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-medium">{{ vendor.store_name_en }}</h3>
                                <p class="text-gray-600">{{ vendor.store_name_ar }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <Link :href="route('vendors.edit', vendor.id)"
                                    class="px-4 py-2 text-black bg-slate-400 edit-button">
                                {{ $t('Edit') }}
                                </Link>
                                <button @click="toggleStatus" :class="{
                                    'bg-red-600 hover:bg-red-700': vendor.is_active,
                                    'bg-green-600 hover:bg-green-700': !vendor.is_active,
                                }" class="px-4 py-2 text-white rounded-md">
                                    {{ vendor.is_active ? $t('Deactivate') : $t('Activate') }}
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h4 class="mb-4 text-lg font-medium">{{ $t('Vendor Information') }}</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Name') }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ vendor.name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Email') }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ vendor.email }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Status')
                                        }}</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            <span :class="{
                                                'bg-green-100 text-green-800': vendor.vendor_status === 'approved',
                                                'bg-yellow-100 text-yellow-800': vendor.vendor_status === 'pending',
                                                'bg-red-100 text-red-800': vendor.vendor_status === 'rejected',
                                            }" class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full">
                                                {{ $t(vendor.vendor_status) }}
                                            </span>
                                            <span v-if="!vendor.is_active"
                                                class="inline-flex px-2 ml-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">
                                                {{ $t('Inactive') }}
                                            </span>
                                        </p>
                                    </div>
                                    <div v-if="vendor.vendor_status === 'rejected' && vendor.rejection_reason">
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Rejection Reason')
                                        }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ vendor.rejection_reason }}</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="mb-4 text-lg font-medium">{{ $t('Store Information') }}</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Commercial Registration')}}</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            {{ vendor.commercial_registration_number }}
                                        </p>
                                        <a v-if="vendor.commercial_registration_image"
                                            :href="'/storage/' + vendor.commercial_registration_image" target="_blank"
                                            class="mt-2 inline-block text-sm text-indigo-600 hover:text-indigo-900">
                                            {{ $t('View Document') }}
                                        </a>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('IBAN') }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ vendor.iban }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Location')
                                        }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ vendor.city?.name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Logo') }}</label>
                                        <img v-if="vendor.logo" :src="'/storage/' + vendor.logo"
                                            class="mt-2 h-20 w-20 rounded-full object-cover" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Branches Section -->
                        <div v-if="branches && branches.length > 0" class="mt-8">
                            <h4 class="mb-4 text-lg font-medium">{{ $t('Branches') }}</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                <div v-for="branch in branches" :key="branch.id" 
                                    class="p-4 bg-gray-50 rounded-lg border">
                                    <div class="flex items-start justify-between mb-3">
                                        <h5 class="text-md font-medium text-gray-900">{{ branch.name }}</h5>
                                        <span :class="{
                                            'bg-green-100 text-green-800': branch.is_active,
                                            'bg-red-100 text-red-800': !branch.is_active,
                                        }" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ branch.is_active ? $t('Active') : $t('Inactive') }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div v-if="branch.city">
                                            <label class="block text-xs font-medium text-gray-600">{{ $t('City') }}</label>
                                            <p class="text-sm text-gray-900">{{ branch.city.name }}</p>
                                        </div>
                                        
                                        <div v-if="branch.contact_number">
                                            <label class="block text-xs font-medium text-gray-600">{{ $t('Contact Number') }}</label>
                                            <p class="text-sm text-gray-900">{{ branch.contact_number }}</p>
                                        </div>
                                        
                                        <div v-if="branch.contact_email">
                                            <label class="block text-xs font-medium text-gray-600">{{ $t('Contact Email') }}</label>
                                            <p class="text-sm text-gray-900">{{ branch.contact_email }}</p>
                                        </div>
                                        
                                        <div v-if="branch.number_of_available_items !== null && branch.number_of_available_items !== undefined">
                                            <label class="block text-xs font-medium text-gray-600">{{ $t('Available Items') }}</label>
                                            <p class="text-sm text-gray-900">{{ branch.number_of_available_items }}</p>
                                        </div>
                                        
                                        <!-- Debug info (uncomment to see raw data) -->
                                        <!-- <div class="text-xs text-gray-500 bg-gray-100 p-2 rounded">
                                            <strong>Debug:</strong><br>
                                            Working Days: {{ branch.working_days }}<br>
                                            Working Hours: {{ branch.working_hours }}<br>
                                            Parsed Days: {{ getWorkingDaysText(branch.working_days) }}<br>
                                            Parsed Hours: {{ JSON.stringify(getWorkingHours(branch.working_days, branch.working_hours)) }}
                                        </div> -->
                                        
                                        <div v-if="getWorkingDaysText(branch.working_days).length > 0">
                                            <label class="block text-xs font-medium text-gray-600">{{ $t('Working Days') }}</label>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                <span v-for="day in getWorkingDaysText(branch.working_days)" :key="day"
                                                    class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                                    {{ day }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div v-if="Object.keys(getWorkingHours(branch.working_days, branch.working_hours)).length > 0">
                                            <label class="block text-xs font-medium text-gray-600">{{ $t('Working Hours') }}</label>
                                            <div class="text-sm text-gray-900">
                                                <div v-for="(hours, day) in getWorkingHours(branch.working_days, branch.working_hours)" :key="day" class="text-xs">
                                                    <span class="font-medium">{{ getDayName(day) }}:</span> 
                                                    {{ hours.from }} - {{ hours.to }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Show message if no working days/hours are available -->
                                        <div v-if="!branch.working_days || getWorkingDaysText(branch.working_days).length === 0" class="text-xs text-gray-500 italic">
                                            {{ $t('No working days specified') }}
                                        </div>
                                        
                                        <div v-if="branch.logo" class="mt-2">
                                            <label class="block text-xs font-medium text-gray-600">{{ $t('Branch Logo') }}</label>
                                            <img :src="'/storage/' + branch.logo" 
                                                class="mt-1 h-12 w-12 rounded object-cover" 
                                                :alt="branch.name + ' logo'" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="vendor.vendor_status === 'pending'" class="mt-8">
                            <h4 class="mb-4 text-lg font-medium">{{ $t('Review Application') }}</h4>
                            <div class="flex space-x-4">
                                <button @click="approveVendor"
                                    class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">
                                    {{ $t('Approve') }}
                                </button>
                                <button @click="showRejectForm = true"
                                    class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700">
                                    {{ $t('Reject') }}
                                </button>
                            </div>

                            <div v-if="showRejectForm" class="mt-4 p-4 bg-gray-50 rounded-md">
                                <form @submit.prevent="rejectVendor">
                                    <div class="mb-4">
                                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">
                                            {{ $t('Rejection Reason') }}
                                        </label>
                                        <textarea id="rejection_reason" v-model="rejectionReason" rows="3"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :placeholder="$t('Please enter the reason for rejection...')"
                                            required></textarea>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="button" @click="showRejectForm = false"
                                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                            {{ $t('Cancel') }}
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700">
                                            {{ $t('Submit Rejection') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    vendor: Object,
    branches: Array,
});

const showRejectForm = ref(false);
const rejectionReason = ref('');

// Define weekDays array similar to Edit.vue
const weekDays = [
    { value: 0, label: t('Sunday') },
    { value: 1, label: t('Monday') },
    { value: 2, label: t('Tuesday') },
    { value: 3, label: t('Wednesday') },
    { value: 4, label: t('Thursday') },
    { value: 5, label: t('Friday') },
    { value: 6, label: t('Saturday') },
];

// Helper function to convert working days numbers to text
const getWorkingDaysText = (workingDays) => {
    if (!workingDays) return [];
    
    // Handle different data formats
    let parsedDays;
    try {
        if (typeof workingDays === 'string') {
            // Handle empty strings or "null" strings
            if (workingDays.trim() === '' || workingDays === 'null') return [];
            parsedDays = JSON.parse(workingDays);
        } else if (Array.isArray(workingDays)) {
            parsedDays = workingDays;
        } else {
            console.log('Unexpected working_days format:', workingDays);
            return [];
        }
    } catch (e) {
        console.error('Error parsing working days:', workingDays, e);
        return [];
    }
    
    if (!Array.isArray(parsedDays)) {
        console.log('Working days is not an array after parsing:', parsedDays);
        return [];
    }
    
    return parsedDays
        .map(day => {
            const dayNum = parseInt(day);
            const weekDay = weekDays.find(wd => wd.value === dayNum);
            return weekDay ? weekDay.label : `Day ${day}`;
        })
        .filter(Boolean); // Remove any undefined values
};

const getDayName = (day) => {
    const dayNum = parseInt(day);
    const weekDay = weekDays.find(wd => wd.value === dayNum);
    return weekDay ? weekDay.label : `Day ${day}`;
};

// Helper function to parse and format working hours
const getWorkingHours = (workingDays, workingHours) => {
    if (!workingDays || !workingHours) return {};
    
    // Parse JSON strings if they're strings
    let parsedDays, parsedHours;
    try {
        // Parse working days
        if (typeof workingDays === 'string') {
            if (workingDays.trim() === '' || workingDays === 'null') return {};
            parsedDays = JSON.parse(workingDays);
        } else if (Array.isArray(workingDays)) {
            parsedDays = workingDays;
        } else {
            console.log('Unexpected working_days format in hours:', workingDays);
            return {};
        }
        
        // Parse working hours
        if (typeof workingHours === 'string') {
            if (workingHours.trim() === '' || workingHours === 'null') return {};
            parsedHours = JSON.parse(workingHours);
        } else if (Array.isArray(workingHours)) {
            parsedHours = workingHours;
        } else if (typeof workingHours === 'object') {
            // Handle case where it's already an object
            parsedHours = workingHours;
        } else {
            console.log('Unexpected working_hours format:', workingHours);
            return {};
        }
    } catch (e) {
        console.error('Error parsing working hours:', { workingDays, workingHours }, e);
        return {};
    }
    
    if (!Array.isArray(parsedDays)) {
        console.log('Working days is not an array in hours function:', parsedDays);
        return {};
    }
    
    // Handle different working hours formats
    const hoursMap = {};
    
    if (Array.isArray(parsedHours)) {
        // Format: [{"open": "09:00", "close": "17:00"}, ...]
        parsedDays.forEach((day, index) => {
            if (parsedHours[index]) {
                hoursMap[day] = {
                    from: parsedHours[index].open || parsedHours[index].from,
                    to: parsedHours[index].close || parsedHours[index].to
                };
            }
        });
    } else if (typeof parsedHours === 'object') {
        // Format: {"0": {"from": "09:00", "to": "17:00"}, ...}
        parsedDays.forEach(day => {
            if (parsedHours[day]) {
                hoursMap[day] = {
                    from: parsedHours[day].from || parsedHours[day].open,
                    to: parsedHours[day].to || parsedHours[day].close
                };
            }
        });
    }
    
    return hoursMap;
};

const toggleStatus = async () => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('Do you want to change this vendor\'s status?'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('Yes, proceed'),
        cancelButtonText: t('Cancel'),
    });

    if (result.isConfirmed) {
        router.patch(route('vendors.toggle-status', props.vendor.id), {
            onSuccess: () => {
                Swal.fire(
                    t('Success'),
                    t('Vendor status has been updated'),
                    'success'
                );
            },
            onError: () => {
                Swal.fire(
                    t('Error'),
                    t('Failed to update vendor status'),
                    'error'
                );
            }
        });
    }
};

const approveVendor = async () => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('Do you want to approve this vendor?'),
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: t('Yes, approve'),
        cancelButtonText: t('Cancel'),
    });

    if (result.isConfirmed) {
        router.post(route('vendors.approve', props.vendor.id), {
            onSuccess: () => {
                Swal.fire(
                    t('Approved'),
                    t('Vendor has been approved'),
                    'success'
                );
            },
            onError: () => {
                Swal.fire(
                    t('Error'),
                    t('Failed to approve vendor'),
                    'error'
                );
            }
        });
    }
};

const rejectVendor = async () => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('Do you want to reject this vendor?'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('Yes, reject'),
        cancelButtonText: t('Cancel'),
    });

    if (result.isConfirmed) {
        router.post(route('vendors.reject', props.vendor.id), {
            rejection_reason: rejectionReason.value,
            onSuccess: () => {
                showRejectForm.value = false;
                Swal.fire(
                    t('Rejected'),
                    t('Vendor has been rejected'),
                    'success'
                );
            },
            onError: () => {
                Swal.fire(
                    t('Error'),
                    t('Failed to reject vendor'),
                    'error'
                );
            }
        });
    }
};
</script>

<style scoped>
button {
    height: 40px;
    margin-left: 5px;
}

.edit-button {
    height: 40px;
    width: 100px;
    background-color: rgb(226, 226, 226) !important;
    border: 1px solid rgb(0, 0, 0) !important;
    border-radius: 5px;
    text-align: center;
}
</style>
