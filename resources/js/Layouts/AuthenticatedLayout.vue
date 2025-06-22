<template>
    <div :class="{ 'rtl-layout': isRTL }">
        <header id="header" class="header fixed-top d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-between">
                <Link class="logo d-flex align-items-center" :href="route('dashboard')">
                    <img src="/dashboard-assets/img/logo2.png" alt="" />
                </Link>
                <i class="bi bi-list toggle-sidebar-btn" @click="toggleBodyClass"></i>
            </div>

            <nav class="header-nav ms-auto">
                <ul class="d-flex align-items-center">
                    <li class="nav-item dropdown">
                        <SwitchLang />
                    </li>

                    <li class="nav-item dropdown">
                        <Link class="nav-link nav-icon" :href="route('notification.index')">
                            <i class="bi bi-bell"></i>
                            <span class="badge bg-primary badge-number">{{ currentNotificationCount }}</span>
                        </Link>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                            <li class="dropdown-header">
                                You have {{ currentNotificationCount }} new notifications
                                <Link :href="route('notification.index')" class="badge rounded-pill bg-primary p-2 ms-2">
                                    View all
                                </Link>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown pe-3">
                        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                            <img :src="user.avatar" alt="Profile Avatar" class="rounded-circle" />
                            <span class="d-none d-md-block dropdown-toggle ps-2">{{ user.name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                            <li class="dropdown-header">
                                <h6>{{ user.name }}</h6>
                                <span>{{ user.email }}</span>
                            </li>
                            <li><hr class="dropdown-divider" /></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" :href="route('profile.edit')">
                                    <i class="bi bi-person"></i>
                                    <span>{{ $t('my_profile') }}</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider" /></li>
                            <li>
                                <Link :href="route('logout')" method="post" as="button" class="dropdown-item d-flex align-items-center">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>{{ $t('log_out') }}</span>
                                </Link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </header>

        <div
            v-if="showVendorNotificationToast"
            class="fixed-top position-fixed bg-white border-start border-4 border-success shadow-lg rounded p-3"
            style="top: 80px; right: 20px; max-width: 400px; z-index: 9999;"
        >
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

        <Sidebar :permissions="page.props.Permissions" />

        <main id="main" class="main">
            <div
                v-if="flashSuccess"
                class="alert alert-success bg-success text-light border-0 alert-dismissible fade show"
                role="alert"
            >
                {{ flashSuccess }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div
                v-if="flashError"
                class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show"
                role="alert"
            >
                {{ flashError }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <main>
                <slot />
            </main>
        </main>

        <div v-if="!isAudioUnlocked" class="sound-enable-overlay">
            <div class="card p-4 shadow-lg text-center rounded-3">
                <i class="bi bi-volume-mute-fill fs-1 text-danger mb-3"></i>
                <h5 class="mb-2">Enable Sound Notifications</h5>
                <p class="mb-3 text-muted">Your browser requires an interaction to play sounds. Click below to enable.</p>
                <button @click="unlockAudioAndHideMessage" class="btn btn-primary btn-lg">
                    <i class="bi bi-volume-up-fill me-2"></i> Enable Sounds Now
                </button>
                <small class="mt-3 text-muted">You only need to do this once per session.</small>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Sidebar from '@/Components/SideBar.vue';
import SwitchLang from '@/Components/SwitchLang.vue';

// Access page props
const page = usePage();

// State for sidebar toggle
const isBodyActive = ref(false);

// Vendor notification state
const showVendorNotificationToast = ref(false);
const vendorNotificationTitle = ref('');
const vendorNotificationMessage = ref('');
const currentNotificationCount = ref(page.props.auth.notificationCount || 0);

// Audio state - Initialize from sessionStorage
const isAudioUnlocked = ref(sessionStorage.getItem('audioUnlocked') === 'true');
const pendingNotifications = ref([]);
const audioUnlockAttempted = ref(sessionStorage.getItem('audioUnlockAttempted') === 'true');
let notificationSound = null;
let fallbackSound = null;
let pusher = null;
let channel = null;

// Computed properties
const isRTL = computed(() => page.props.locale === 'ar');
const flashSuccess = computed(() => page.props.flash.success);
const flashError = computed(() => page.props.flash.error);
const currentLanguage = computed(() => page.props.locale || 'en');
const user = computed(() => page.props.auth);
const notificationCount = computed(() => Math.min(page.props.auth.notificationCount, 9));

// Initialize audio objects
const initializeAudio = () => {
    try {
        notificationSound = new Audio('/sounds/notifications.mp3');
        fallbackSound = new Audio('/sounds/notification.wav');

        // Configure audio
        notificationSound.volume = 0.8;
        fallbackSound.volume = 0.8;
        notificationSound.preload = 'auto'; // Preload for faster playback
        fallbackSound.preload = 'auto';     // Preload for faster playback

        // Add load event listeners for debugging
        notificationSound.addEventListener('canplaythrough', () => {
            console.log('Notification sound loaded and ready');
        });
        fallbackSound.addEventListener('canplaythrough', () => {
            console.log('Fallback sound loaded and ready');
        });

        // Add error listeners
        notificationSound.addEventListener('error', (e) => {
            console.error('Error loading notification sound:', e);
        });
        fallbackSound.addEventListener('error', (e) => {
            console.error('Error loading fallback sound:', e);
        });

        console.log('Audio objects initialized');
    } catch (error) {
        console.error('Error initializing audio:', error);
    }
};

// Simple and reliable audio unlock
// This function attempts to unlock audio.
// Crucially, it MUST be called as a direct result of a user interaction
// (e.g., a click, keydown, touch event) for it to work due to browser autoplay policies.
const unlockAudio = async () => {
    if (isAudioUnlocked.value) {
        console.log('Audio already unlocked');
        await processPendingNotifications();
        return true;
    }

    if (audioUnlockAttempted.value) {
        console.log('Audio unlock already attempted, skipping');
        return false;
    }

    audioUnlockAttempted.value = true;
    sessionStorage.setItem('audioUnlockAttempted', 'true'); // Persist attempt status
    console.log('Attempting to unlock audio...');

    try {
        // Method 1: Try to play the actual notification sound at very low volume
        // This is the preferred method as it uses the sound directly.
        if (notificationSound) {
            const originalVolume = notificationSound.volume;
            notificationSound.volume = 0.01; // Play silently
            const playPromise = notificationSound.play();
            await playPromise;

            // Immediately pause and reset
            notificationSound.pause();
            notificationSound.currentTime = 0;
            notificationSound.volume = originalVolume;

            isAudioUnlocked.value = true;
            sessionStorage.setItem('audioUnlocked', 'true'); // Persist unlocked status
            console.log('Audio unlocked successfully with notification sound');
            await processPendingNotifications();
            return true;
        }
    } catch (error) {
        console.log('Primary unlock method failed:', error.message);
    }

    try {
        // Method 2: Create minimal audio context as a fallback
        // Some browsers might prefer this method for unlocking.
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();

        // Create a very short, silent sound
        const buffer = audioContext.createBuffer(1, 1, 22050);
        const source = audioContext.createBufferSource();
        source.buffer = buffer;
        source.connect(audioContext.destination);
        source.start();

        // Resume context if suspended
        if (audioContext.state === 'suspended') {
            await audioContext.resume();
        }

        isAudioUnlocked.value = true;
        sessionStorage.setItem('audioUnlocked', 'true'); // Persist unlocked status
        console.log('Audio unlocked with AudioContext');
        await processPendingNotifications();
        return true;
    } catch (error) {
        console.log('AudioContext unlock failed:', error.message);
    }

    console.warn('Audio unlock failed - will queue notifications');
    return false;
};

// Process all queued notifications
const processPendingNotifications = async () => {
    if (pendingNotifications.value.length === 0) {
        return;
    }

    console.log(`Processing ${pendingNotifications.value.length} queued notifications`);

    // Play only the most recent notification to avoid spam
    const latestNotification = pendingNotifications.value[pendingNotifications.value.length - 1];
    pendingNotifications.value = []; // Clear all queued notifications

    try {
        const sound = latestNotification.sound;
        sound.currentTime = 0;
        await sound.play();
        console.log('Queued notification played successfully');
    } catch (error) {
        console.error('Error playing queued notification:', error.message);

        // Try fallback
        if (latestNotification.sound === notificationSound && fallbackSound) {
            try {
                fallbackSound.currentTime = 0;
                await fallbackSound.play();
                console.log('Fallback sound played successfully');
            } catch (fallbackError) {
                console.error('Fallback sound also failed:', fallbackError.message);
            }
        }
    }
};

// Play notification sound
const playNotificationSound = async (sound = notificationSound) => {
    if (!sound) {
        console.error('Sound object is null');
        return;
    }

    // THIS IS THE CRUCIAL PART FOR BROWSER POLICY COMPLIANCE:
    // If audio is not unlocked, queue the notification and RETURN.
    // Do NOT attempt to play the sound directly if isAudioUnlocked is false.
    if (!isAudioUnlocked.value) {
        pendingNotifications.value.push({ sound });
        console.log(`Audio not unlocked, notification queued. Total queued: ${pendingNotifications.value.length}`);

        // Inform the user that sound requires interaction
        console.warn('🔇 Audio is locked. Please interact with the page (e.g., click or type) to enable notification sounds.');
        return; // <--- IMPORTANT: Stop execution here if not unlocked
    }

    try {
        sound.currentTime = 0;
        const playPromise = sound.play();
        await playPromise;
        console.log('✅ Notification sound played successfully');
    } catch (error) {
        // This catch block will only be hit if a play() attempt fails *after* audio was supposedly unlocked.
        // This is less common but can happen if the context suspends again (e.g., long backgrounding).
        console.error('❌ Error playing notification sound:', error.message);

        // Try fallback sound if primary failed
        if (sound === notificationSound && fallbackSound) {
            try {
                fallbackSound.currentTime = 0;
                await fallbackSound.play();
                console.log('✅ Fallback sound played successfully');
            } catch (fallbackError) {
                console.error('❌ Fallback sound also failed:', fallbackError.message);
            }
        }
    }
};

// Enhanced toggle sidebar
const toggleBodyClass = async () => {
    isBodyActive.value = !isBodyActive.value;
    document.body.classList.toggle('toggle-sidebar', isBodyActive.value);

    // This is one of the user interactions that can unlock audio.
    if (!isAudioUnlocked.value) {
        console.log('Sidebar toggled - attempting audio unlock');
        await unlockAudio();
    }
};

// Language change handler
const changeLanguage = (event) => {
    const selectedLanguage = event.target.value;
    const currentUrl = window.location.origin;
    const newUrl = `${currentUrl}/lang/change?lang=${selectedLanguage}`;
    window.location.href = newUrl;
};

// Hide vendor notification toast
const hideVendorNotificationToast = () => {
    showVendorNotificationToast.value = false;
};

// User interaction handler - this is the key to unlocking audio!
// This function will be called on various user gestures.
const handleUserInteraction = async (event) => {
    if (isAudioUnlocked.value) {
        // If audio is already unlocked, simply return.
        // The popup is hidden by v-if="!isAudioUnlocked"
        // and we don't need to re-attempt unlock or remove listeners.
        return;
    }

    console.log(`🎵 User interaction detected (${event.type}) - attempting audio unlock`);

    const success = await unlockAudio();

    if (success) {
        // No need to explicitly remove listeners here.
        // The `if (isAudioUnlocked.value)` check at the top
        // of this function will handle it for subsequent clicks.
        console.log('🎉 Audio unlocked by general interaction!');
    }
};

// Specific handler for the "Enable Sounds" button in the overlay
const unlockAudioAndHideMessage = async () => {
    const success = await unlockAudio();
    if (success) {
        console.log('Overlay button clicked, audio unlocked, message will hide.');
    } else {
        console.warn('Overlay button click failed to unlock audio.');
    }
};

// Add interaction listeners
// We add listeners for common user gestures that are allowed to unlock audio.
const addInteractionListeners = () => {
    // These listeners remain active throughout the component's lifecycle.
    // The `handleUserInteraction` function's internal check (`if (isAudioUnlocked.value)`)
    // prevents unnecessary re-unlock attempts after the first successful one.
    const events = ['click', 'touchstart', 'keydown', 'mousedown', 'touchend'];
    events.forEach((event) => {
        document.addEventListener(event, handleUserInteraction, {
            capture: true, // Use capture to ensure we catch events early
            passive: true // Indicate listeners won't call preventDefault
        });
    });
    console.log('👂 Added interaction listeners for audio unlock');
};

// Remove interaction listeners (mostly for cleanup on unmount)
const removeInteractionListeners = () => {
    const events = ['click', 'touchstart', 'keydown', 'mousedown', 'touchend'];
    events.forEach((event) => {
        document.removeEventListener(event, handleUserInteraction, { capture: true });
    });
    console.log('🚫 Removed interaction listeners');
};

// Test sound function (for debugging) - Can be triggered manually for dev
const testSound = async () => {
    console.log('🧪 Testing sound manually...');

    if (!isAudioUnlocked.value) {
        const unlocked = await unlockAudio();
        if (!unlocked) {
            console.error('❌ Could not unlock audio for testing');
            return;
        }
    }

    await playNotificationSound(notificationSound);
};

// Setup Pusher
const setupPusher = () => {
    try {
        // Enable Pusher logging in development
        if (import.meta.env.DEV) {
            Pusher.logToConsole = true;
        }

        pusher = new Pusher('6f4401ea11a233a51f96', {
            cluster: 'eu',
        });

        channel = pusher.subscribe(`vendor-notifications.${user.value.id}`);
        channel.bind('vendor-notification', (data) => {
            // Update UI
            showVendorNotificationToast.value = true;
            vendorNotificationTitle.value = data.title || 'New Notification';
            vendorNotificationMessage.value = data.message || 'You have a new notification.';
            currentNotificationCount.value += 1;

            // Play sound - this will either play immediately if unlocked, or queue if locked.
            playNotificationSound(notificationSound);
        });

        channel = pusher.subscribe(`admin-notifications.${user.value.id}`);
        channel.bind('admin-notification', (data) => {
            // Update UI
            showVendorNotificationToast.value = true;
            vendorNotificationTitle.value = data.title || 'New Notification';
            vendorNotificationMessage.value = data.message || 'You have a new notification.';
            currentNotificationCount.value += 1;

            // Play sound - this will either play immediately if unlocked, or queue if locked.
            playNotificationSound(notificationSound);
        });
        console.log('✅ Pusher initialized and subscribed to channel');
    } catch (error) {
        console.error('❌ Error setting up Pusher:', error);
    }
};

// Check sound file accessibility
const checkSoundFiles = async () => {
    const soundFiles = [
        { name: 'notifications.mp3', path: '/sounds/notifications.mp3' },
        { name: 'notification.wav', path: '/sounds/notification.wav' }
    ];

    for (const file of soundFiles) {
        try {
            const response = await fetch(file.path, { method: 'HEAD' });
            if (response.ok) {
                console.log(`✅ Sound file accessible: ${file.name}`);
            } else {
                console.error(`❌ Sound file not accessible: ${file.name} (Status: ${response.status})`);
            }
        } catch (error) {
            console.error(`❌ Error checking ${file.name}:`, error);
        }
    }
};

// Component lifecycle
onMounted(async () => {
    console.log('🚀 AuthenticatedLayout mounted');
    console.log('Initial audioUnlocked state from sessionStorage:', isAudioUnlocked.value);

    // Initialize everything
    initializeAudio();
    // Add interaction listeners. They will only try to unlock if `isAudioUnlocked` is false.
    addInteractionListeners();
    setupPusher();

    // Check sound files
    await nextTick();
    await checkSoundFiles();

    // The console message now assumes the overlay will guide the user.
    if (!isAudioUnlocked.value) {
        console.log('💡 Notification sounds are currently paused by your browser. The "Enable Sounds" overlay is visible to prompt interaction.');
    }
});

onUnmounted(() => {
    console.log('🛑 AuthenticatedLayout unmounting...');

    // Clean up Pusher
    if (channel) {
        channel.unbind_all();
        pusher.unsubscribe(`vendor-notifications.${user.value.id}`);
    }
    if (pusher) {
        pusher.disconnect();
    }

    // Remove interaction listeners to prevent memory leaks,
    // especially important if the layout might not be completely replaced.
    removeInteractionListeners();

    // Clean up audio
    if (notificationSound) {
        notificationSound.pause();
        notificationSound.src = '';
        notificationSound = null;
    }
    if (fallbackSound) {
        fallbackSound.pause();
        fallbackSound.src = '';
        fallbackSound = null;
    }

    // Clear state
    pendingNotifications.value = [];
    // isAudioUnlocked and audioUnlockAttempted are persisted in sessionStorage
    // so no need to reset them here if we want them to carry over.
    // Only clear sessionStorage if you want it to be per-mount, not per-session.
    // sessionStorage.removeItem('audioUnlocked'); // Uncomment if you want to reset on unmount
    // sessionStorage.removeItem('audioUnlockAttempted'); // Uncomment if you want to reset on unmount

    console.log('✅ AuthenticatedLayout cleanup complete');
});

// Expose testSound for debugging (remove in production)
if (import.meta.env.DEV) {
    window.testSound = testSound;
}
</script>

<style scoped>
/* Basic styling for the sound enable overlay */
.sound-enable-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7); /* Darker overlay to make it prominent */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000; /* Ensure it's on top of everything */
    backdrop-filter: blur(5px); /* Optional: add a blur effect */
    -webkit-backdrop-filter: blur(5px); /* Safari support */
}

.sound-enable-overlay .card {
    min-width: 350px;
    max-width: 90%; /* Responsive width */
    background-color: #fff;
    color: #333;
    animation: fadeInScale 0.3s ease-out forwards; /* Simple animation */
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>