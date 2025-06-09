<template>
    <AuthenticatedLayout>

        <div class="pagetitle row">
            <BreadcrumbComponent
                :pageTitle="$t('notifications')"
                :homeLabel="$t('home')"
            />
        </div>


        <section class="section dashboard">
            <div class="row justify-center">
                <div class="col-lg-12">
                    <el-card shadow="hover" class="w-full">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title text-lg font-semibold mb-0">
                                {{ $t("recent_notifications") }}
                            </h5>
                            <el-button 
                                v-if="hasUnreadNotifications" 
                                type="primary" 
                                size="small"
                                @click="markAllAsRead"
                                :loading="markingAllAsRead"
                            >
                                {{ $t("mark_all_as_read") }}
                            </el-button>
                        </div>
                        <el-divider></el-divider>

                        <div v-if="notifications.data.length" class="activity">
                            <div 
                                v-for="notification in notifications.data" 
                                :key="notification.id"
                                @click="markNotificationAsRead(notification)"
                                class="activity-item d-flex align-items-start p-3 border-bottom position-relative cursor-pointer hover:bg-gray-50 transition-colors"
                                :class="{ 'bg-light': !notification.read_at }"
                                style="cursor: pointer;"
                            >
                                <!-- Unread indicator -->
                                <div v-if="!notification.read_at" 
                                     class="position-absolute" 
                                     style="left: 10px; top: 50%; transform: translateY(-50%);">
                                    <span class="badge bg-primary rounded-circle p-1" style="width: 8px; height: 8px;"></span>
                                </div>
                                
                                <div class="activity-icon me-3 flex-shrink-0" :class="{ 'ms-3': !notification.read_at }">
                                    <i class="bi bi-bell text-primary fs-4"></i>
                                </div>
                                
                                <div class="activity-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1" :class="{ 'fw-bold': !notification.read_at }">
                                                {{ getNotificationTitle(notification) }}
                                            </h6>
                                            <p class="mb-1 text-muted small">
                                                {{ getNotificationMessage(notification) }}
                                            </p>
                                            <p class="mb-0 text-muted small">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ formatDate(notification.created_at) }}
                                            </p>
                                        </div>
                                        
                                        <!-- Read status indicator -->
                                        <div class="flex-shrink-0">
                                            <span v-if="notification.read_at" class="badge bg-success small">
                                                {{ $t('read') }}
                                            </span>
                                            <span v-else class="badge bg-warning small">
                                                {{ $t('unread') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <el-empty
                            v-else
                            :description="$t('no_notifications_found')"
                        ></el-empty>
                    </el-card>
                </div>
            </div>

            <Pagination :links="notifications.links" />
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Link } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";
import { ref, computed } from "vue";
import axios from 'axios';

import {
    ElBadge,
    ElCard,
    ElDivider,
    ElEmpty,
    ElIcon,
    ElBreadcrumb,
    ElBreadcrumbItem,
    ElButton,
    ElMessage,
} from "element-plus";

const props = defineProps({
    notifications: Object,
});

const page = usePage();
const markingAllAsRead = ref(false);

const hasUnreadNotifications = computed(() => {
    return props.notifications.data.some(notification => !notification.read_at);
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleString("ar-EG", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
    });
};

const getNotificationTitle = (notification) => {
    // Try to get title from notification data
    if (notification.data && notification.data.title) {
        // Check if title is an object with language keys
        if (typeof notification.data.title === 'object' && notification.data.title !== null) {
            const locale = page.props.locale || 'en';
            return notification.data.title[locale] || notification.data.title.en || notification.data.title.ar || 'New Notification';
        }
        // If it's a string, return it directly
        if (typeof notification.data.title === 'string') {
            return notification.data.title;
        }
    }
    
    // Fallback based on notification type
    const notificationType = notification.type || '';
    if (notificationType.includes('NewOrderNotification')) {
        return 'New Order Received';
    } else if (notificationType.includes('NewGoldPieceNotification')) {
        return 'New Gold Piece Available';
    } else if (notificationType.includes('OrderStatusNotification')) {
        return 'Order Status Updated';
    }
    
    return 'New Notification';
};

const getNotificationMessage = (notification) => {
    // Try to get message from notification data
    if (notification.data && notification.data.message) {
        // Check if message is an object with language keys
        if (typeof notification.data.message === 'object' && notification.data.message !== null) {
            const locale = page.props.locale || 'en';
            return notification.data.message[locale] || notification.data.message.en || notification.data.message.ar || 'You have a new notification.';
        }
        // If it's a string, return it directly
        if (typeof notification.data.message === 'string') {
            return notification.data.message;
        }
    }
    
    // Try to get body from notification data
    if (notification.data && notification.data.body) {
        // Check if body is an object with language keys
        if (typeof notification.data.body === 'object' && notification.data.body !== null) {
            const locale = page.props.locale || 'en';
            return notification.data.body[locale] || notification.data.body.en || notification.data.body.ar || 'You have a new notification.';
        }
        // If it's a string, return it directly
        if (typeof notification.data.body === 'string') {
            return notification.data.body;
        }
    }
    
    // Fallback based on notification type
    const notificationType = notification.type || '';
    if (notificationType.includes('NewOrderNotification')) {
        return 'You have received a new order that requires your attention.';
    } else if (notificationType.includes('NewGoldPieceNotification')) {
        return 'A new gold piece is available for purchase or rental.';
    } else if (notificationType.includes('OrderStatusNotification')) {
        return 'The status of one of your orders has been updated.';
    }
    
    return 'You have a new notification.';
};

const updateNotificationCount = () => {
    // Dispatch event to update the header notification count
    window.dispatchEvent(new CustomEvent('notification-count-updated'));
};

const markNotificationAsRead = async (notification) => {
    if (notification.read_at) {
        console.log('Notification already read');
        return; // Already read
    }

    try {
        console.log('Marking notification as read:', notification.id);
        
        const response = await axios.post(`/notifications/${notification.id}/read`);
        
        if (response.data.success) {
            // Update the notification locally
            notification.read_at = new Date().toISOString();
            
            ElMessage({
                message: 'Notification marked as read',
                type: 'success',
                duration: 2000,
            });
            
            // Update notification count in header
            updateNotificationCount();
        } else {
            throw new Error(response.data.error || 'Unknown error');
        }
        
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
        
        let errorMessage = 'Failed to mark notification as read';
        if (error.response && error.response.data && error.response.data.error) {
            errorMessage = error.response.data.error;
        } else if (error.message) {
            errorMessage = error.message;
        }
        
        ElMessage({
            message: errorMessage,
            type: 'error',
            duration: 3000,
        });
    }
};

const markAllAsRead = async () => {
    markingAllAsRead.value = true;
    
    try {
        await axios.post('/notifications/read-all');
        
        // Update all notifications locally
        props.notifications.data.forEach(notification => {
            if (!notification.read_at) {
                notification.read_at = new Date().toISOString();
            }
        });
        
        ElMessage({
            message: 'All notifications marked as read',
            type: 'success',
            duration: 3000,
        });
        
        // Update notification count in header
        updateNotificationCount();
        
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error);
        ElMessage({
            message: 'Failed to mark all notifications as read',
            type: 'error',
            duration: 3000,
        });
    } finally {
        markingAllAsRead.value = false;
    }
};

const arrowDirection = computed(
    () => `el-icon-arrow-${page.props.locale === "ar" ? "right" : "left"}`
);
</script>

<style scoped>
.notification-item {
    transition: background-color 0.3s;
}
.notification-item:hover {
    background-color: #f0f9ff;
}

.notification-unread {
    background-color: #f8fafc;
    border-left: 4px solid #3b82f6;
}

.cursor-pointer {
    cursor: pointer;
}

:deep(.el-breadcrumb) {
    direction: rtl;
}

[dir="rtl"] :deep(.el-breadcrumb__separator) {
    transform: rotate(180deg);
}
</style>
