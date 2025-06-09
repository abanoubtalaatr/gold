import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token for requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Enable Pusher for real-time notifications
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    enabledTransports: ['ws', 'wss'],
    disabledTransports: ['xhr_polling', 'xhr_streaming'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': token ? token.content : '',
            'Authorization': 'Bearer ' + (window.authToken || ''),
        },
    },
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                axios.post('/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                }, {
                    headers: {
                        'X-CSRF-TOKEN': token ? token.content : '',
                    }
                })
                .then(response => {
                    console.log('Channel authorization successful for:', channel.name);
                    callback(false, response.data);
                })
                .catch(error => {
                    console.error('Channel authorization failed for:', channel.name, error);
                    callback(true, error);
                });
            }
        };
    },
});

// Log Echo connection status
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Echo: Successfully connected to Pusher');
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('Echo: Disconnected from Pusher');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('Echo: Connection error:', error);
});

// Log when Echo is ready
console.log('Echo initialized with config:', {
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
