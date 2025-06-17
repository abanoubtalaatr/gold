<template>
    <div :class="{ 'rtl-layout': isRTL }">
        <!-- ======= Header ======= -->
        <header id="header" class="header fixed-top d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-between">
                <Link
                    class="logo d-flex align-items-center"
                    :href="route('dashboard')"
                >
                    <img src="/dashboard-assets/img/logo2.png" alt="" />
                    <!-- <span class="d-none d-lg-block">Fudex</span> -->
                </Link>
                <i
                    class="bi bi-list toggle-sidebar-btn"
                    @click="toggleBodyClass"
                ></i>
            </div>
            <!-- End Logo -->

            <nav class="header-nav ms-auto">
                <ul class="d-flex align-items-center">
                    <!-- <li class="nav-item d-block d-lg-none">
                        <a class="nav-link nav-icon search-bar-toggle " href="#">
                            <i class="bi bi-search"></i>
                        </a>
                    </li> -->
                    <!-- End Search Icon-->
                    <li class="nav-item dropdown">
                        <!-- <select class="form-control changeLang"  v-model="currentLanguage" @change="changeLanguage">
                            <option value="" selected> {{ $t('language') }} 🌍 </option>
                            <option value="en"> {{ $t('english') }}</option>
                            <option value="ar">{{ $t('arabic')   }}</option>
                        </select> -->
                        <SwitchLang />
                    </li>

                    <li class="nav-item dropdown">
                         <Link
                            class="nav-link nav-icon"
                            :href="route('notification.index')"
                        >
                            <i class="bi bi-bell"></i>
                            <span class="badge bg-primary badge-number">{{
                                currentNotificationCount
                            }}</span>
                        </Link>
                        <!-- End Notification Icon -->
                        <ul
                            class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications"
                        >
                            <li class="dropdown-header">
                                You have {{ currentNotificationCount }} new notifications
                                <Link
                                    :href="route('notification.index')"
                                    class="badge rounded-pill bg-primary p-2 ms-2"
                                >View all</Link>
                            </li>
                        </ul>
                    </li>
                    <!--
                    <li class="nav-item dropdown">

                        <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-chat-left-text"></i>
                            <span class="badge bg-success badge-number">3</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                            <li class="dropdown-header">
                                You have 3 new messages
                                <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li class="message-item">
                                <a href="#">
                                    <img src="/dashboard-assets/img/messages-1.jpg" alt="" class="rounded-circle">
                                    <div>
                                        <h4>Maria Hudson</h4>
                                        <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                        <p>4 hrs. ago</p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li class="message-item">
                                <a href="#">
                                    <img src="/dashboard-assets/img/messages-2.jpg" alt="" class="rounded-circle">
                                    <div>
                                        <h4>Anna Nelson</h4>
                                        <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                        <p>6 hrs. ago</p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li class="message-item">
                                <a href="#">
                                    <img src="/dashboard-assets/img/messages-3.jpg" alt="" class="rounded-circle">
                                    <div>
                                        <h4>David Muldon</h4>
                                        <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                                        <p>8 hrs. ago</p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li class="dropdown-footer">
                                <a href="#">Show all messages</a>
                            </li>

                        </ul>

                    </li> -->
                    <!-- End Messages Nav -->

                    <li class="nav-item dropdown pe-3">
                        <a
                            class="nav-link nav-profile d-flex align-items-center pe-0"
                            href="#"
                            data-bs-toggle="dropdown"
                        >
                            <img
                                :src="user.avatar"
                                alt="Profile Avatar"
                                class="rounded-circle"
                            />
                            <span
                                class="d-none d-md-block dropdown-toggle ps-2"
                                >{{ user.name }}</span
                            >
                        </a>
                        <!-- End Profile Iamge Icon -->

                        <ul
                            class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile"
                        >
                            <li class="dropdown-header">
                                <h6>{{ user.name }}</h6>
                                <span>{{ user.email }}</span>
                            </li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>

                            <li>
                                <a
                                    class="dropdown-item d-flex align-items-center"
                                    :href="route('profile.edit')"
                                >
                                    <i class="bi bi-person"></i>
                                    <span>{{ $t("my_profile") }} </span>
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider" />
                            </li>

                            <li>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="dropdown-item d-flex align-items-center"
                                >
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>{{ $t("log_out") }}</span>
                                </Link>
                            </li>
                        </ul>
                        <!-- End Profile Dropdown Items -->
                    </li>
                    <!-- End Profile Nav -->
                </ul>
            </nav>
            <!-- End Icons Navigation -->
        </header>

        <!-- Real-time notification toast for vendors -->
        <div v-if="showVendorNotificationToast" 
             class="fixed-top position-fixed bg-white border-start border-4 border-success shadow-lg rounded p-3"
             style="top: 80px; right: 20px; max-width: 400px; z-index: 9999;">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0 me-3">
                    <i class="bi bi-bell-fill text-success fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">{{ vendorNotificationTitle }}</h6>
                    <p class="mb-0 small text-muted">{{ vendorNotificationMessage }}</p>
                </div>
                <button @click="hideVendorNotificationToast" class="btn-close ms-2" type="button"></button>
            </div>
        </div>

        <!-- Include the Sidebar here -->
        <Sidebar :permissions="page.props.Permissions" />

        <!-- Include the main content here -->

        <main id="main" class="main">
            <div
                v-if="flashSuccess"
                class="alert alert-success bg-success text-light border-0 alert-dismissible fade show"
                role="alert"
            >
                {{ flashSuccess }}
                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>

            <div
                v-if="flashError"
                class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show"
                role="alert"
            >
                {{ flashError }}
                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>

            <main>
                <slot />
            </main>
        </main>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import SwitchLang from "@/Components/SwitchLang.vue";

const showingNavigationDropdown = ref(false);
const page = usePage();

// Vendor notification state
const showVendorNotificationToast = ref(false);
const vendorNotificationTitle = ref('');
const vendorNotificationMessage = ref('');
const currentNotificationCount = ref(page.props.auth.notificationCount || 0);

const isRTL = computed(() => {
    return page.props.locale === "ar";
});

// Setup vendor notifications using reliable AJAX polling (primary) and SSE (fallback)
const setupVendorNotifications = () => {
    // Check if user is authenticated
    const authUser = page.props.auth?.user || page.props.auth;
    if (!authUser || !authUser.id) {
        console.error('User not authenticated for notifications.');
        return;
    }
    
    const userId = authUser.id;
    const isVendor = authUser.is_vendor || false;
    const locale = page.props.locale || 'en';
    
    console.log('Setting up notifications for user:', userId, 'isVendor:', isVendor);
    console.log('User roles:', authUser.roles);
    
    // Use AJAX polling as primary method (more reliable)
    setupPollingNotifications();
};

// Enhanced polling method as primary notification system
const setupPollingNotifications = () => {
    console.log('🔄 Setting up AJAX polling notifications...');
    
    let lastNotificationCount = currentNotificationCount.value;
    
    // Get stored values from localStorage to persist across page navigations
    const getStoredTimestamp = () => {
        const stored = localStorage.getItem('notification_last_check');
        if (stored) {
            try {
                const timestamp = new Date(stored);
                // Don't use timestamps older than 1 hour
                if (timestamp > new Date(Date.now() - 60 * 60 * 1000)) {
                    console.log('📅 Using stored timestamp:', timestamp.toISOString());
                    return timestamp.toISOString();
                }
            } catch (e) {
                console.warn('Invalid stored timestamp:', stored);
            }
        }
        // Default to 30 seconds ago for new sessions (very recent to avoid old notifications)
        const defaultTimestamp = new Date(Date.now() - 30 * 1000).toISOString();
        console.log('📅 Using default timestamp (30 seconds ago):', defaultTimestamp);
        return defaultTimestamp;
    };
    
    const getStoredProcessedIds = () => {
        try {
            const stored = localStorage.getItem('processed_notification_ids');
            if (stored) {
                const ids = JSON.parse(stored);
                // Only keep IDs from the last hour to prevent memory buildup
                const oneHourAgo = Date.now() - 60 * 60 * 1000;
                const filteredIds = ids.filter(item => {
                    return item.timestamp > oneHourAgo;
                });
                return new Set(filteredIds.map(item => item.id));
            }
        } catch (e) {
            console.warn('Failed to parse stored notification IDs:', e);
        }
        return new Set();
    };
    
    const saveProcessedIds = (processedIds) => {
        try {
            const idsWithTimestamp = Array.from(processedIds).map(id => ({
                id: id,
                timestamp: Date.now()
            }));
            localStorage.setItem('processed_notification_ids', JSON.stringify(idsWithTimestamp));
        } catch (e) {
            console.warn('Failed to save processed notification IDs:', e);
        }
    };
    
    let lastProcessedTimestamp = getStoredTimestamp();
    let processedNotificationIds = getStoredProcessedIds();
    
    console.log('📋 Restored from localStorage:', {
        lastProcessedTimestamp,
        processedCount: processedNotificationIds.size
    });
    
    const pollForNotifications = async () => {
        try {
            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const response = await fetch(`/notifications/poll?last_check=${encodeURIComponent(lastProcessedTimestamp)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'include' // Include cookies for session-based auth
            });
            const data = await response.json();
            
            console.log('🔍 Polling response:', {
                timestamp: lastProcessedTimestamp,
                found: data.notifications?.length || 0,
                total_unread: data.total_unread
            });
            
            if (data.notifications && data.notifications.length > 0) {
                console.log('📈 All notifications found since', lastProcessedTimestamp, ':', data.notifications.map(n => ({
                    id: n.id,
                    created_at: n.created_at,
                    already_processed: processedNotificationIds.has(n.id)
                })));
                
                // Filter out notifications we've already processed
                const newNotifications = data.notifications.filter(notification => {
                    // Skip if already processed
                    if (processedNotificationIds.has(notification.id)) {
                        return false;
                    }
                    
                    // Skip notifications older than 10 minutes (safeguard against old notifications)
                    const notificationAge = Date.now() - new Date(notification.created_at).getTime();
                    const tenMinutesInMs = 10 * 60 * 1000;
                    if (notificationAge > tenMinutesInMs) {
                        console.log('⏳ Skipping old notification (older than 10 minutes):', {
                            id: notification.id,
                            created_at: notification.created_at,
                            age_minutes: Math.round(notificationAge / 60000)
                        });
                        // Add to processed list to avoid checking again
                        processedNotificationIds.add(notification.id);
                        return false;
                    }
                    
                    return true;
                });
                
                if (newNotifications.length > 0) {
                    console.log('🆕 Truly new notifications (not processed before):', newNotifications.map(n => ({
                        id: n.id,
                        created_at: n.created_at,
                        title: n.data?.title
                    })));
                    
                    // Process each new notification
                    newNotifications.forEach(notification => {
                        console.log('🔔 Processing new notification:', {
                            id: notification.id,
                            created_at: notification.created_at,
                            title: notification.data?.title
                        });
                        handleVendorNotification(notification.data, page.props.locale || 'en');
                        // Mark this notification as processed
                        processedNotificationIds.add(notification.id);
                    });
                    
                    // Save processed IDs to localStorage
                    saveProcessedIds(processedNotificationIds);
                    
                    // Update the last processed timestamp to the latest notification's timestamp
                    const latestNotification = newNotifications.sort((a, b) => 
                        new Date(b.created_at) - new Date(a.created_at))[0];
                    if (latestNotification) {
                        lastProcessedTimestamp = latestNotification.created_at;
                        localStorage.setItem('notification_last_check', lastProcessedTimestamp);
                        console.log('⏰ Updated and saved last processed timestamp to:', lastProcessedTimestamp);
                    }
                } else {
                    console.log('📋 No new notifications to process (all already seen)');
                }
            } else {
                console.log('📭 No notifications found since', lastProcessedTimestamp);
                // Update timestamp even when no notifications to prevent querying too far back
                const newTimestamp = new Date(Date.now() - 30 * 1000).toISOString(); // 30 seconds ago
                if (newTimestamp > lastProcessedTimestamp) {
                    lastProcessedTimestamp = newTimestamp;
                    localStorage.setItem('notification_last_check', lastProcessedTimestamp);
                    console.log('⏰ Updated timestamp to prevent old queries:', lastProcessedTimestamp);
                }
            }
            
            // Update notification count
            if (data.total_unread !== undefined) {
                const newCount = Math.min(data.total_unread, 99);
                if (newCount !== currentNotificationCount.value) {
                    console.log(`📊 Notification count updated: ${currentNotificationCount.value} → ${newCount}`);
                    currentNotificationCount.value = newCount;
                    
                    // If count decreased, some notifications were read - clean up processed IDs
                    if (newCount < lastNotificationCount) {
                        console.log('📤 Notifications were marked as read, cleaning up processed IDs');
                        // Keep only recent notification IDs to prevent memory buildup
                        const recentIds = data.notifications ? 
                            new Set(data.notifications.map(n => n.id)) : new Set();
                        processedNotificationIds = new Set([...processedNotificationIds].filter(id => 
                            recentIds.has(id)
                        ));
                        saveProcessedIds(processedNotificationIds);
                    }
                    
                    // Dispatch event for other components
                    window.dispatchEvent(new CustomEvent('notification-count-fetched', {
                        detail: { count: newCount }
                    }));
                }
                lastNotificationCount = newCount;
            }
            
        } catch (error) {
            console.error('❌ Failed to poll notifications:', error);
        }
    };
    
    // Initial poll immediately
    pollForNotifications();
    
    // Poll every 2 seconds for better real-time experience (reduced from 5 seconds)
    const pollingInterval = setInterval(pollForNotifications, 2000);
    
    // Store reference for cleanup
    window.notificationPollingInterval = pollingInterval;
    // Store processed IDs for cleanup - now using localStorage
    window.processedNotificationIds = processedNotificationIds;
    
    console.log('📡 AJAX polling notification system initialized (2-second interval) with persistent storage');
};

// SSE method as backup (if needed)
const setupSSENotifications = () => {
    console.log('🔄 Setting up SSE notifications as backup...');
    
    // Check if user is authenticated
    const authUser = page.props.auth?.user || page.props.auth;
    if (!authUser || !authUser.id) {
        console.error('User not authenticated for SSE notifications.');
        return;
    }
    
    const locale = page.props.locale || 'en';
    
    // Set up Server-Sent Events connection
    // TEMPORARILY DISABLED - SSE causing test notifications
    /*
    if (typeof EventSource !== 'undefined') {
        // Close existing connection if any
        if (window.notificationEventSource) {
            window.notificationEventSource.close();
        }
        
        const eventSource = new EventSource('/notifications/stream');
        let reconnectAttempts = 0;
        const maxReconnectAttempts = 3;
        
        eventSource.onopen = function(event) {
            console.log('✅ SSE connection opened successfully');
            reconnectAttempts = 0; // Reset on successful connection
        };
        
        eventSource.onmessage = function(event) {
            try {
                const data = JSON.parse(event.data);
                console.log('📨 SSE message received:', data);
                
                if (data.type === 'notification') {
                    console.log('🔔 New notification received via SSE:', data);
                    // Handle the notification data
                    handleVendorNotification(data.data, locale);
                    updateNotificationCount();
                } else if (data.type === 'connected') {
                    console.log('🔗 SSE connected for user:', data.user_id, 'at:', data.timestamp);
                } else if (data.type === 'heartbeat') {
                    console.log('💓 SSE heartbeat:', data.timestamp, 'iteration:', data.iteration);
                } else if (data.type === 'error') {
                    console.error('❌ SSE server error:', data.message);
                } else if (data.type === 'closing') {
                    console.log('🔌 SSE connection closing:', data.reason);
                    // Don't auto-reconnect since we're using polling as primary
                }
            } catch (error) {
                console.error('❌ Failed to parse SSE message:', error, event.data);
            }
        };
        
        eventSource.onerror = function(event) {
            console.error('❌ SSE connection error:', event);
            if (eventSource.readyState === EventSource.CLOSED) {
                console.log('🔄 SSE connection closed - using polling as primary');
                // Don't auto-reconnect since polling is our primary method
            }
        };
        
        // Store reference for cleanup
        window.notificationEventSource = eventSource;
        
        console.log('📡 SSE notification system initialized as backup');
    } else {
        console.log('❌ Server-Sent Events not supported by this browser - using polling only');
    }
    */
    
    console.log('📡 SSE notification system temporarily disabled - using polling only');
};

const handleVendorNotification = (event, locale) => {
    console.log('🔔 Handling vendor notification:', event);
    
    // Helper function to extract text based on locale
    const getLocalizedText = (textData, fallback = '') => {
        if (!textData) return fallback;
        
        // If it's already a string, return it
        if (typeof textData === 'string') {
            return textData;
        }
        
        // If it's an object with language keys, extract the appropriate one
        if (typeof textData === 'object' && textData !== null) {
            return textData[locale] || textData.en || textData.ar || fallback;
        }
        
        return fallback;
    };
    
    // Get the appropriate message and title based on locale
    const title = getLocalizedText(event.title, 'New Notification');
    const message = getLocalizedText(event.message, 'You have a new notification');
    
    console.log('📱 Showing notification toast:', { title, message, locale });
    
    // Show toast notification
    showVendorNotificationToast.value = true;
    vendorNotificationTitle.value = title;
    vendorNotificationMessage.value = message;
    
    // Play sound if enabled (default to true if not specified)
    const soundEnabled = event.sound_enabled !== false; // Default to true
    if (soundEnabled) {
        console.log('🔊 Playing notification sound...');
        playNotificationSound();
    } else {
        console.log('🔇 Sound disabled for this notification');
    }
    
    // Auto hide after 8 seconds
    setTimeout(() => {
        hideVendorNotificationToast();
    }, 8000);
};

const playNotificationSound = () => {
    try {
        console.log('Attempting to play notification sound...');
        
        // Try to use the Web Audio API
        if (window.AudioContext || window.webkitAudioContext) {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            // Create a simple beep sound
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime); // 800Hz frequency
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
            
            console.log('Notification sound played successfully');
        } else {
            console.log('Web Audio API not supported, trying HTML5 audio...');
            // Fallback: Create a data URL beep sound
            const audioElement = new Audio();
            audioElement.volume = 0.3;
            // Generate a simple beep using data URL
            const freq = 800;
            const sampleRate = 44100;
            const duration = 0.5;
            const samples = sampleRate * duration;
            const arrayBuffer = new ArrayBuffer(samples * 2);
            const dataView = new DataView(arrayBuffer);
            
            for (let i = 0; i < samples; i++) {
                const sample = Math.sin(2 * Math.PI * freq * i / sampleRate) * 0.3 * 32767;
                dataView.setInt16(i * 2, sample, true);
            }
            
            const blob = new Blob([arrayBuffer], { type: 'audio/wav' });
            const url = URL.createObjectURL(blob);
            audioElement.src = url;
            audioElement.play().then(() => {
                console.log('Fallback notification sound played');
                URL.revokeObjectURL(url);
            }).catch(error => {
                console.error('Failed to play fallback sound:', error);
            });
        }
    } catch (error) {
        console.error('Failed to play notification sound:', error);
        // Final fallback - try browser's default notification sound
        try {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('New Order Notification', {
                    body: 'You have received a new order.',
                    icon: '/favicon.ico',
                    silent: false
                });
            }
        } catch (notificationError) {
            console.error('Failed to show browser notification:', notificationError);
        }
    }
};

const updateNotificationCount = () => {
    console.log('Updating notification count...');
    // Increment the notification count in real-time
    currentNotificationCount.value = Math.min(currentNotificationCount.value + 1, 99);
    
    // Dispatch custom event so other parts of the app can react
    window.dispatchEvent(new CustomEvent('notification-count-updated', {
        detail: { count: currentNotificationCount.value }
    }));
    
    // Fetch the actual count from server to ensure accuracy
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            console.log('Notification count from server:', data);
            if (data.count !== undefined) {
                currentNotificationCount.value = Math.min(data.count, 99);
                // Dispatch updated count
                window.dispatchEvent(new CustomEvent('notification-count-fetched', {
                    detail: { count: currentNotificationCount.value }
                }));
            }
        })
        .catch(e => {
            console.error('Failed to fetch notification count:', e);
        });
};

const refreshNotificationCount = () => {
    console.log('Refreshing notification count...');
    // Fetch the actual count from server without incrementing
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            console.log('Refreshed notification count from server:', data);
            if (data.count !== undefined) {
                currentNotificationCount.value = Math.min(data.count, 99);
                // Dispatch updated count
                window.dispatchEvent(new CustomEvent('notification-count-fetched', {
                    detail: { count: currentNotificationCount.value }
                }));
            }
        })
        .catch(e => {
            console.error('Failed to refresh notification count:', e);
        });
};

// Function to clear processed notification IDs (can be called when notifications are marked as read)
const clearProcessedNotifications = () => {
    if (window.processedNotificationIds) {
        console.log('🧹 Manually clearing processed notification IDs...');
        window.processedNotificationIds.clear();
    }
    
    // Also clear localStorage data
    try {
        localStorage.removeItem('processed_notification_ids');
        localStorage.removeItem('notification_last_check');
        console.log('🗑️ Cleared persistent notification data from localStorage');
    } catch (e) {
        console.warn('Failed to clear localStorage notification data:', e);
    }
};

// Make it globally available for other components to call
window.clearProcessedNotifications = clearProcessedNotifications;

// Global function to completely reset notification system (useful for logout, etc.)
const resetNotificationSystem = () => {
    console.log('🔄 Completely resetting notification system...');
    
    // Clear localStorage
    try {
        localStorage.removeItem('processed_notification_ids');
        localStorage.removeItem('notification_last_check');
    } catch (e) {
        console.warn('Failed to clear localStorage:', e);
    }
    
    // Clear window references
    if (window.processedNotificationIds) {
        window.processedNotificationIds.clear();
    }
    
    // Reset current count
    currentNotificationCount.value = 0;
    
    console.log('✅ Notification system completely reset');
};

// Make reset function globally available
window.resetNotificationSystem = resetNotificationSystem;

const hideVendorNotificationToast = () => {
    showVendorNotificationToast.value = false;
    vendorNotificationTitle.value = '';
    vendorNotificationMessage.value = '';
};

// Component lifecycle
onMounted(() => {
    // One-time cleanup of potentially stale localStorage data
    const lastClearKey = 'notification_system_last_clear';
    const lastClear = localStorage.getItem(lastClearKey);
    const now = Date.now();
    
    // Clear localStorage if it hasn't been cleared in the last hour or if it's never been cleared
    if (!lastClear || (now - parseInt(lastClear)) > 60 * 60 * 1000) {
        console.log('🧹 Performing one-time cleanup of notification localStorage...');
        localStorage.removeItem('processed_notification_ids');
        localStorage.removeItem('notification_last_check');
        localStorage.setItem(lastClearKey, now.toString());
        console.log('✅ Notification localStorage cleaned');
    }
    
    // Start notifications quickly (reduced delay from 1000ms to 500ms)
    setTimeout(() => {
        setupVendorNotifications();
    }, 500);
    
    // Listen for notification count updates from other pages
    window.addEventListener('notification-count-updated', refreshNotificationCount);
    window.addEventListener('notification-count-fetched', (event) => {
        if (event.detail && event.detail.count !== undefined) {
            currentNotificationCount.value = Math.min(event.detail.count, 99);
        }
    });
    
    // Initial notification count fetch
    refreshNotificationCount();
});

onUnmounted(() => {
    // Clean up SSE connection
    if (window.notificationEventSource) {
        console.log('🔌 Closing SSE connection...');
        window.notificationEventSource.close();
        window.notificationEventSource = null;
    }
    
    // Clean up polling interval
    if (window.notificationPollingInterval) {
        console.log('⏹️ Clearing notification polling interval...');
        clearInterval(window.notificationPollingInterval);
        window.notificationPollingInterval = null;
    }
    
    // Clean up window reference (but keep localStorage for persistence)
    if (window.processedNotificationIds) {
        console.log('🗑️ Clearing window reference to processed notification IDs...');
        window.processedNotificationIds = null;
    }
    
    // Remove event listeners
    window.removeEventListener('notification-count-updated', refreshNotificationCount);
    window.removeEventListener('notification-count-fetched', (event) => {
        if (event.detail && event.detail.count !== undefined) {
            currentNotificationCount.value = Math.min(event.detail.count, 99);
        }
    });
    
    console.log('🧹 Notification system cleanup completed (localStorage preserved for persistence)');
});
</script>

<script>
import Sidebar from "@/Components/SideBar.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const flashSuccess = computed(() => page.props.flash.success);
const flashError = computed(() => page.props.flash.error);

const page = usePage();
const currentLanguage = computed(() => page.props.locale || "en"); // 'en' كقيمة افتراضية

const user = computed(() => page.props.auth);

const notificationCount = computed(() =>
    Math.min(page.props.auth.notificationCount, 9)
);

const changeLanguage = (event) => {
    const selectedLanguage = event.target.value;
    const currentUrl = window.location.origin; // Get the current app URL
    const newUrl = `${currentUrl}/lang/change?lang=${selectedLanguage}`; // Construct the new URL
    window.location.href = newUrl; // Redirect to the new URL
};

const isBodyActive = ref(false);

const toggleBodyClass = () => {
    // alert(1);
    isBodyActive.value = !isBodyActive.value;

    if (isBodyActive.value) {
        document.body.classList.add("toggle-sidebar");
    } else {
        document.body.classList.remove("toggle-sidebar");
    }
};

export default {
    components: {
        Sidebar,
    },
    data() {
        return {
            parentMessage: "Hello from Parent",
        };
    },
};
</script>
