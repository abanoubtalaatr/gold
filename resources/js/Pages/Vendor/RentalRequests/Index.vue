<template>

    <Head title="Rental Requests" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Rental Requests') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Filters -->
                        <!-- Filters -->
                        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                            <!-- Search Input (Full Width) -->
                            
                            <!-- Other Filters (3 Columns Each on Medium Screens) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 w-full">
                                <div class="md:w-full">
                                    <select v-model="form.branch_id"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters">
                                        <option value="">{{ $t('All Branches') }}</option>
                                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                            {{ branch.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="md:w-full">
                                    <select v-model="form.status"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters">
                                        <option value="">{{ $t('All Statuses') }}</option>
                                        <option v-for="status in statuses" :key="status" :value="status">
                                            {{ formatStatus(status) }}
                                        </option>
                                    </select>
                                </div>
                                <div class="md:w-full">
                                    <select v-model="form.rental_status"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters">
                                        <option value="">{{ $t('All Rental Periods') }}</option>
                                        <option value="current">{{ $t('Current Rentals') }}</option>
                                        <option value="finished">{{ $t('Finished Rentals') }}</option>
                                        <option value="future">{{ $t('Future Rentals') }}</option>
                                    </select>
                                </div>
                                
                                <div class="md:w-full">
                                    <select v-model="form.date_filter"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters">
                                        <option value="">{{ $t('All Dates') }}</option>
                                        <option value="today">{{ $t('Today') }}</option>
                                        <option value="week">{{ $t('This Week') }}</option>
                                        <option value="custom">{{ $t('Custom Range') }}</option>
                                    </select>
                                </div>
                                <div v-if="form.date_filter === 'custom'" class="md:w-full">
                                    <input v-model="form.date_from" type="date"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters" />
                                </div>
                                <div v-if="form.date_filter === 'custom'" class="md:w-full">
                                    <input v-model="form.date_to" type="date"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters" />
                                </div>
                                <div class="md:w-full">
                                    <button @click="resetFilters"
                                        class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        {{ $t('Reset') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Orders') }}</h3>
                        <div v-if="orders?.data?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Order ID') }}</th>

                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('User') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Gold Piece') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Branch') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Start Date') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('End Date') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Price') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Status') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="order in orders.data" :key="order.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.id }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <img v-if="order.user?.avatar" :src="order.user.avatar"
                                                    class="h-8 w-8 rounded-full object-cover mr-2" alt="User avatar" />
                                                <div>
                                                    {{ order.user?.name || 'N/A' }}<br />
                                                    {{ order.user?.mobile || 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <button @click="showGoldPieceDetails(order.gold_piece)"
                                                class="text-indigo-600 p-2 rounded hover:text-indigo-800 hover:underline font-medium">
                                               {{ order.gold_piece.name?? 'N/A' }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                            order.branch?.name ||
                                            'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.start_date ? formatDate(order.start_date, true) : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.end_date ? formatDate(order.end_date, true) : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                            order.total_price }} {{
                                            $t('SAR') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(order.status)]">
                                                {{ formatStatus(order.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2 flex-wrap">
                                                <!-- Accept button for pending approval -->
                                                <button v-if="order.allowed_actions?.includes('approve')"
                                                    @click="acceptOrderDirect(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors duration-200">
                                                    {{ $t('Accept') }}
                                                </button>

                                                <!-- Reject button for pending approval -->
                                                <button v-if="order.allowed_actions?.includes('reject')"
                                                    @click="openRejectModal(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors duration-200">
                                                    {{ $t('Reject') }}
                                                </button>

                                                <!-- Mark as sent button for approved orders -->
                                                <button v-if="order.allowed_actions?.includes('mark_as_sent')"
                                                    @click="markAsSent(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                                    {{ $t('Mark as Sent') }}
                                                </button>

                                                <!-- Confirm rental button for piece sent status -->
                                                <button v-if="order.allowed_actions?.includes('confirm_rental')"
                                                    @click="confirmRental(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700 transition-colors duration-200">
                                                    {{ $t('Confirm Rental') }}
                                                </button>

                                                <!-- View invoice button -->
                                                <button v-if="order.invoice" @click="showInvoice(order)"
                                                    class="px-3 py-1 text-sm font-medium text-blue-600 border border-blue-600 rounded-md hover:bg-blue-50 transition-colors duration-200">
                                                    {{ $t('View Invoice') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <Pagination :links="orders.links" class="mt-6" />
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <p class="text-xl font-semibold">{{ $t('No orders found.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time notification toast -->
        <div v-if="showNotificationToast"
            class="fixed top-4 right-4 bg-white border-l-4 border-green-500 shadow-lg rounded-lg p-4 max-w-md z-50 transform transition-all duration-300"
            :class="notificationToastClass">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ notificationMessage }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="hideNotificationToast" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Audio element for sound notifications -->
        <audio ref="notificationSound" preload="auto">
            <source src="/sounds/notification.mp3" type="audio/mpeg">
            <source src="/sounds/notification.wav" type="audio/wav">
        </audio>

        <!-- Details Modal -->
        <Modal :show="showDetailsModal" @close="closeDetailsModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ $t('Order Details') }}</h2>
                <div v-if="selectedOrder" class="mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>{{ $t('Order ID') }}:</strong> {{ selectedOrder.id }}</p>
                            <p><strong>{{ $t('Order Type') }}:</strong> {{ selectedOrder.order_type === 'rental' ?
                                $t('Rental')
                                : $t('Sale') }}</p>
                            <p><strong>{{ $t('Created At') }}:</strong> {{ formatDate(selectedOrder.created_at) }}</p>
                            <p><strong>{{ $t('User') }}:</strong> {{ selectedOrder.user?.name || 'N/A' }}</p>
                            <p><strong>{{ $t('User Email') }}:</strong> {{ selectedOrder.user?.email || 'N/A' }}</p>
                            <p><strong>{{ $t('Piece Name') }}:</strong> {{ selectedOrder.goldPiece?.name || 'N/A' }}</p>
                            <p><strong>{{ $t('Description') }}:</strong> {{ selectedOrder.goldPiece?.description ||
                                'N/A' }}</p>
                            <p><strong>{{ $t('Weight') }}:</strong> {{ selectedOrder.goldPiece?.weight || 'N/A' }} {{
                                $t('grams') }}</p>
                            <p><strong>{{ $t('Carat') }}:</strong> {{ selectedOrder.goldPiece?.carat || 'N/A' }}</p>
                            <p v-if="selectedOrder.start_date"><strong>{{ $t('Start Date') }}:</strong> {{
                                formatDate(selectedOrder.start_date, true) }}</p>
                            <p v-if="selectedOrder.end_date"><strong>{{ $t('End Date') }}:</strong> {{
                                formatDate(selectedOrder.end_date, true) }}</p>
                            <p><strong>{{ $t('Price') }}:</strong> {{ selectedOrder.total_price }} {{ $t('SAR') }}</p>
                            <p><strong>{{ $t('Status') }}:</strong> {{ formatStatus(selectedOrder.status) }}</p>
                            <p><strong>{{ $t('Branch') }}:</strong> {{ selectedOrder.branch?.name || 'N/A' }}</p>
                        </div>
                        <div>
                            <p><strong>{{ $t('Images') }}:</strong></p>
                            <div v-if="selectedOrder.goldPiece?.images?.length" class="flex flex-wrap gap-2 mt-2">
                                <img v-for="image in selectedOrder.goldPiece.images" :key="image.id"
                                    :src="'/storage/' + image.path" class="h-20 w-20 object-cover rounded border"
                                    alt="Gold piece image" />
                            </div>
                            <p v-else class="text-gray-500 mt-2">{{ $t('No images available') }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button @click="closeDetailsModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        {{ $t('Close') }}
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Gold Piece Details Modal -->
        <Modal :show="showGoldPieceModal" @close="closeGoldPieceModal" max-width="4xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-medium text-gray-900">{{ $t('Gold Piece Details') }}</h2>
                    <button @click="closeGoldPieceModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                
                <div v-if="selectedGoldPiece" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Basic Information') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $t('Name') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $t('Weight') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.weight || 'N/A' }} {{ $t('grams') }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $t('Carat') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.carat || 'N/A' }}</p>
                            </div>
                            
                            <div v-if="selectedGoldPiece.price_per_day" class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-700">{{ $t('Daily Rental Price') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.price_per_day }} {{ $t('SAR') }}/{{ $t('day') }}</p>
                            </div>
                            <div v-if="selectedGoldPiece.sale_price" class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-700">{{ $t('Sale Price') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.sale_price }} {{ $t('SAR') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="selectedGoldPiece.description" class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Description') }}</h3>
                        <p class="text-sm text-gray-900">{{ selectedGoldPiece.description }}</p>
                    </div>

                    <!-- Images Gallery -->
                    <div v-if="selectedGoldPiece.images?.length" class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Images Gallery') }}</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div v-for="image in selectedGoldPiece.images" :key="image.id" 
                                 class="relative group cursor-pointer"
                                 @click="openImageModal(image)">
                                <img :src="'/storage/' + image.path" 
                                     class="w-full h-32 object-cover rounded-lg border hover:border-indigo-500 transition-colors duration-200"
                                     :alt="`${selectedGoldPiece.name} image`" />
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-opacity duration-200 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Details -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Additional Details') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-if="selectedGoldPiece.category">
                                <p class="text-sm font-medium text-gray-700">{{ $t('Category') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.category }}</p>
                            </div>
                            <div v-if="selectedGoldPiece.brand">
                                <p class="text-sm font-medium text-gray-700">{{ $t('Brand') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.brand }}</p>
                            </div>
                            <div v-if="selectedGoldPiece.created_at">
                                <p class="text-sm font-medium text-gray-700">{{ $t('Added Date') }}</p>
                                <p class="text-sm text-gray-900">{{ formatDate(selectedGoldPiece.created_at) }}</p>
                            </div>
                            <div v-if="selectedGoldPiece.updated_at">
                                <p class="text-sm font-medium text-gray-700">{{ $t('Last Updated') }}</p>
                                <p class="text-sm text-gray-900">{{ formatDate(selectedGoldPiece.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button @click="closeGoldPieceModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                        {{ $t('Close') }}
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Image Preview Modal -->
        <Modal :show="showImageModal" @close="closeImageModal" max-width="3xl">
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $t('Image Preview') }}</h3>
                    <button @click="closeImageModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div v-if="selectedImage" class="text-center">
                    <img :src="'/storage/' + selectedImage.path" 
                         class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg"
                         :alt="selectedGoldPiece?.name + ' image'" />
                </div>
            </div>
        </Modal>

        <!-- Reject Order Modal -->
        <Modal :show="showRejectModal" @close="closeRejectModal">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">{{ $t('Reject Order') }}</h2>
                    <p class="mb-4 text-sm text-gray-600">
                        {{ $t('Are you sure you want to reject this order? This action cannot be undone.') }}
                    </p>
                    <div v-if="selectedOrder" class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="text-sm text-gray-600">
                            <p><strong>{{ $t('Order ID') }}:</strong> {{ selectedOrder.id }}</p>
                            <p><strong>{{ $t('User') }}:</strong> {{ selectedOrder.user?.name || 'N/A' }}</p>
                            <p><strong>{{ $t('Gold Piece') }}:</strong> {{ selectedOrder.goldPiece?.name || 'N/A' }}</p>
                            <p><strong>{{ $t('Price') }}:</strong> {{ selectedOrder.total_price }} {{ $t('SAR') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" @click="closeRejectModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="rejectOrder"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors duration-200">
                        {{ $t('Reject Order') }}
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Invoice Modal -->
        <Modal :show="showInvoiceModal" @close="closeInvoiceModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ $t('Electronic Invoice') }}</h2>
                <div v-if="selectedOrder?.invoice" class="mt-4">
                    <p><strong>{{ $t('Order ID') }}:</strong> {{ selectedOrder.invoice.order_id }}</p>
                    <p><strong>{{ $t('User Name') }}:</strong> {{ selectedOrder.invoice.user_name }}</p>
                    <p><strong>{{ $t('Piece Name') }}:</strong> {{ selectedOrder.invoice.piece_name }}</p>
                    <p><strong>{{ $t('Service Type') }}:</strong> {{ selectedOrder.invoice.service_type === 'rent' ?
                        $t('Rental') : $t('Sale') }}</p>
                    <p v-if="selectedOrder.invoice.rental_days"><strong>{{ $t('Rental Days') }}:</strong> {{
                        selectedOrder.invoice.rental_days }}</p>
                    <p v-if="selectedOrder.invoice.start_date"><strong>{{ $t('Start Date') }}:</strong> {{
                        formatDate(selectedOrder.invoice.start_date, true) }}</p>
                    <p v-if="selectedOrder.invoice.end_date"><strong>{{ $t('End Date') }}:</strong> {{
                        formatDate(selectedOrder.invoice.end_date, true) }}</p>
                    <p><strong>{{ $t('Total Price') }}:</strong> {{ selectedOrder.invoice.total_price }} {{ $t('SAR') }}
                    </p>
                    <p><strong>{{ $t('Branch') }}:</strong> {{ selectedOrder.invoice.branch_name }}</p>
                    <p><strong>{{ $t('Created At') }}:</strong> {{ formatDate(selectedOrder.invoice.created_at) }}</p>
                    <p><strong>{{ $t('Status') }}:</strong> {{ formatStatus(selectedOrder.invoice.status) }}</p>
                </div>
                <div class="mt-6 flex justify-end">
                    <button @click="closeInvoiceModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        {{ $t('Close') }}
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import debounce from 'lodash/debounce';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    orders: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    branches: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    statuses: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const form = useForm({
    search: props.filters.search || '',
    service_type: props.filters.service_type || '',
    branch_id: props.filters.branch_id || '',
    status: props.filters.status || '',
    rental_status: props.filters.rental_status || '',
    piece_name: props.filters.piece_name || '',
    description: props.filters.description || '',
    price_min: props.filters.price_min || '',
    price_max: props.filters.price_max || '',
    date_filter: props.filters.date_filter || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const showDetailsModal = ref(false);
const showInvoiceModal = ref(false);
const showGoldPieceModal = ref(false);
const showImageModal = ref(false);
const selectedOrder = ref(null);
const selectedGoldPiece = ref(null);
const selectedImage = ref(null);
const showRejectModal = ref(false);
const processing = ref(false);

// Real-time notification state
const showNotificationToast = ref(false);
const notificationMessage = ref('');
const notificationToastClass = ref('');
const notificationSound = ref(null);
let echo = null;

const debouncedSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    form.get(route('vendor.rental-requests.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.service_type = '';
    form.branch_id = '';
    form.status = '';
    form.rental_status = '';
    form.piece_name = '';
    form.description = '';
    form.price_min = '';
    form.price_max = '';
    form.date_filter = '';
    form.date_from = '';
    form.date_to = '';

    router.get(route('vendor.rental-requests.index'), {}, {
        preserveState: false,
        preserveScroll: true,
        replace: true
    });
};

const getStatusClass = (status) => {
    switch (status) {
        case 'pending_approval':
            return 'bg-yellow-100 text-yellow-800';
        case 'approved':
            return 'bg-green-100 text-green-800';
        case 'piece_sent':
            return 'bg-blue-100 text-blue-800';
        case 'rented':
            return 'bg-purple-100 text-purple-800';
        case 'available':
            return 'bg-gray-100 text-gray-800';
        case 'sold':
            return 'bg-red-100 text-red-800';
        case 'payment_confirmed':
            return 'bg-blue-100 text-blue-800';
        case 'rejected':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const formatStatus = (status) => {
    // Use proper translations for status values
    const statusTranslations = {
        'pending_approval': t('Pending Approval'),
        'approved': t('Approved'),
        'rejected': t('Rejected'),
        'piece_sent': t('Piece Sent'),
        'rented': t('Rented'),
        'available': t('Available'),
        'sold': t('Sold'),
        'payment_confirmed': t('Payment Confirmed')
    };
    
    return statusTranslations[status] || status
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const formatDate = (date, dateOnly = false) => {
    if (!date) return 'N/A';
    
    const dateObj = new Date(date);
    
    if (dateOnly) {
        return dateObj.toLocaleDateString();
    }
    
    return dateObj.toLocaleString();
};

const showDetails = (order) => {
    selectedOrder.value = order;
    showDetailsModal.value = true;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedOrder.value = null;
};

const showInvoice = (order) => {
    selectedOrder.value = order;
    showInvoiceModal.value = true;
};

const closeInvoiceModal = () => {
    showInvoiceModal.value = false;
    selectedOrder.value = null;
};

const openRejectModal = (order) => {
    selectedOrder.value = order;
    showRejectModal.value = true;
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    selectedOrder.value = null;
};

const acceptOrderDirect = (order) => {
    console.log('Direct accept order function called');
    console.log('Selected order:', order);
    
    if (!order) {
        console.error('No order provided');
        return;
    }

    console.log('Submitting direct accept...');
    console.log('Route URL:', route('vendor.rental-requests.accept', { order: order.id }));
    
    router.post(route('vendor.rental-requests.accept', { order: order.id }), {}, {
        preserveScroll: true,
        onStart: () => {
            console.log('Direct accept request started');
        },
        onSuccess: (response) => {
            console.log('Direct accept request successful:', response);
            applyFilters();
            showNotification(t('Order accepted successfully'), 'success');
        },
        onError: (errors) => {
            console.error('Error accepting order:', errors);
            console.log('Full accept error response:', errors);
            showNotification(errors.message || t('Failed to accept order'), 'error');
        },
        onFinish: () => {
            console.log('Direct accept request finished');
        }
    });
};

const rejectOrder = () => {
    console.log('Reject order function called');
    console.log('Selected order:', selectedOrder.value);
    console.log('Route URL:', route('vendor.rental-requests.reject', { order: selectedOrder.value.id }));
    
    router.post(route('vendor.rental-requests.reject', { order: selectedOrder.value.id }), {}, {
        preserveScroll: true,
        onStart: () => {
            console.log('Reject request started');
        },
        onSuccess: (response) => {
            console.log('Reject request successful:', response);
            closeRejectModal();
            applyFilters();
        },
        onError: (errors) => {
            console.error('Error rejecting order:', errors);
            console.log('Full reject error response:', errors);
        },
        onFinish: () => {
            console.log('Reject request finished');
        }
    });
};

const confirmRental = async (order) => {
    if (!confirm(t('Are you sure you want to confirm this rental?'))) {
        return;
    }

    console.log('Confirming rental for order:', order.id);
    console.log('Order object:', order);

    try {
        processing.value = true;
        await router.post(route('vendor.rental-requests.confirm-rental', { order: order.id }), {}, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                console.log('Success response:', page);
                showNotification(t('Rental confirmed successfully'), 'success');
            },
            onError: (errors) => {
                console.error('Error confirming rental:', errors);
                showNotification(errors.message || t('Failed to confirm rental'), 'error');
            },
            onFinish: () => {
                processing.value = false;
            },
        });
    } catch (error) {
        console.error('Exception confirming rental:', error);
        processing.value = false;
        showNotification(t('Failed to confirm rental'), 'error');
    }
};

// Function to mark order as sent
const markAsSent = async (order) => {
    router.post(route('vendor.rental-requests.mark-sent', { order: order.id }), {}, {
        preserveScroll: true,
        onSuccess: () => {
            applyFilters();
        },
        onError: (errors) => {
            console.error('Error marking order as sent:', errors);
        }
    });
};

// Real-time notification functions
const setupRealTimeNotifications = () => {
    if (window.Echo && page.props?.auth?.user?.id) {
        const userId = page.props.auth.user.id;

        // Listen to private channel for user notifications
        echo = window.Echo.private(`notifications.${userId}`)
            .listen('.rental.status.updated', (e) => {
                handleRealTimeNotification(e);
            });

        // Also listen to branch notifications if user has branches
        if (props.branches?.length > 0) {
            props.branches.forEach(branch => {
                window.Echo.private(`branch.${branch.id}`)
                    .listen('.rental.status.updated', (e) => {
                        handleRealTimeNotification(e);
                    });
            });
        }
    }
};

const handleRealTimeNotification = (event) => {
    const locale = document.documentElement.lang || 'en';

    // Get the appropriate message based on locale
    const message = event.message[locale] || event.message.en || 'Rental status updated';

    // Show toast notification
    showNotificationToast.value = true;
    notificationMessage.value = message;
    notificationToastClass.value = getNotificationClass(event.new_status);

    // Play sound if enabled
    if (event.sound_enabled && notificationSound.value) {
        try {
            notificationSound.value.play().catch(e => {
                console.log('Could not play notification sound:', e);
            });
        } catch (e) {
            console.log('Notification sound error:', e);
        }
    }

    // Auto hide after 5 seconds
    setTimeout(() => {
        hideNotificationToast();
    }, 5000);

    // Refresh the data
    applyFilters();
};

const getNotificationClass = (status) => {
    switch (status) {
        case 'approved':
            return 'border-green-500';
        case 'rejected':
            return 'border-red-500';
        case 'piece_sent':
            return 'border-blue-500';
        case 'rented':
            return 'border-purple-500';
        case 'available':
            return 'border-gray-500';
        default:
            return 'border-blue-500';
    }
};

const hideNotificationToast = () => {
    showNotificationToast.value = false;
    notificationMessage.value = '';
    notificationToastClass.value = '';
};

// Notification helper function
const showNotification = (message, type = 'success') => {
    showNotificationToast.value = true;
    notificationMessage.value = message;
    notificationToastClass.value = type === 'success' ? 'border-green-500' : 'border-red-500';
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        hideNotificationToast();
    }, 5000);
};

const showGoldPieceDetails = (goldPiece) => {
    selectedGoldPiece.value = goldPiece;
    showGoldPieceModal.value = true;
};

const closeGoldPieceModal = () => {
    showGoldPieceModal.value = false;
    selectedGoldPiece.value = null;
};

const openImageModal = (image) => {
    selectedImage.value = image;
    showImageModal.value = true;
};

const closeImageModal = () => {
    showImageModal.value = false;
    selectedImage.value = null;
};

const getGoldPieceStatusClass = (status) => {
    switch (status) {
        case 'available':
            return 'bg-green-100 text-green-800';
        case 'rented':
            return 'bg-blue-100 text-blue-800';
        case 'sold':
            return 'bg-red-100 text-red-800';
        case 'maintenance':
            return 'bg-yellow-100 text-yellow-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

// Component lifecycle
onMounted(() => {
    setupRealTimeNotifications();
});

onUnmounted(() => {
    if (echo) {
        echo.leaveChannel();
    }
});
</script>