/**
 * MyTime Push Notifications Manager
 * Handles service worker registration and push notification subscriptions
 */

class PushNotificationManager {
    constructor() {
        this.serviceWorkerRegistration = null;
        this.isSupported = this.checkSupport();
        this.init();
    }

    /**
     * Check if push notifications are supported
     */
    checkSupport() {
        return 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
    }

    /**
     * Initialize push notifications
     */
    async init() {
        if (!this.isSupported) {
            console.warn('Push notifications are not supported in this browser');
            return;
        }

        try {
            // Register service worker
            await this.registerServiceWorker();

            // Request notification permission
            await this.requestNotificationPermission();

            // Subscribe to push notifications
            await this.subscribeToPushNotifications();

            console.log('Push notifications initialized successfully');
        } catch (error) {
            console.error('Error initializing push notifications:', error);
        }
    }

    /**
     * Register service worker
     */
    async registerServiceWorker() {
        try {
            this.serviceWorkerRegistration = await navigator.serviceWorker.register('/service-worker.js', {
                scope: '/'
            });

            console.log('Service Worker registered successfully:', this.serviceWorkerRegistration);

            // Listen for messages from service worker
            navigator.serviceWorker.addEventListener('message', event => {
                console.log('Message from Service Worker:', event.data);
            });

            return this.serviceWorkerRegistration;
        } catch (error) {
            console.error('Service Worker registration failed:', error);
            throw error;
        }
    }

    /**
     * Request notification permission
     */
    async requestNotificationPermission() {
        if (Notification.permission === 'granted') {
            console.log('Notification permission already granted');
            return true;
        }

        if (Notification.permission !== 'denied') {
            try {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    console.log('Notification permission granted');
                    return true;
                }
            } catch (error) {
                console.error('Error requesting notification permission:', error);
            }
        }

        return false;
    }

    /**
     * Subscribe to push notifications
     */
    async subscribeToPushNotifications() {
        if (!this.serviceWorkerRegistration) {
            console.warn('Service Worker not registered');
            return;
        }

        try {
            // Check if already subscribed
            let subscription = await this.serviceWorkerRegistration.pushManager.getSubscription();

            if (!subscription) {
                // Subscribe to push notifications
                subscription = await this.serviceWorkerRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(this.getPublicKey())
                });

                console.log('Subscribed to push notifications:', subscription);
            } else {
                console.log('Already subscribed to push notifications');
            }

            // Send subscription to server
            await this.sendSubscriptionToServer(subscription);

            return subscription;
        } catch (error) {
            console.error('Error subscribing to push notifications:', error);
            throw error;
        }
    }

    /**
     * Send subscription to server
     */
    async sendSubscriptionToServer(subscription) {
        try {
            const response = await fetch('/push-notifications/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(subscription)
            });

            const data = await response.json();

            if (data.success) {
                console.log('Subscription sent to server successfully');
                return true;
            } else {
                console.error('Failed to send subscription to server:', data.message);
                return false;
            }
        } catch (error) {
            console.error('Error sending subscription to server:', error);
            throw error;
        }
    }

    /**
     * Unsubscribe from push notifications
     */
    async unsubscribeToPushNotifications() {
        if (!this.serviceWorkerRegistration) {
            console.warn('Service Worker not registered');
            return;
        }

        try {
            const subscription = await this.serviceWorkerRegistration.pushManager.getSubscription();

            if (subscription) {
                await subscription.unsubscribe();
                console.log('Unsubscribed from push notifications');

                // Notify server
                await fetch('/push-notifications/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                return true;
            }
        } catch (error) {
            console.error('Error unsubscribing from push notifications:', error);
            throw error;
        }

        return false;
    }

    /**
     * Toggle push notifications
     */
    async togglePushNotifications(enabled) {
        try {
            const response = await fetch('/push-notifications/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ enabled })
            });

            const data = await response.json();

            if (data.success) {
                console.log('Push notifications toggled:', data.message);
                return true;
            } else {
                console.error('Failed to toggle push notifications:', data.message);
                return false;
            }
        } catch (error) {
            console.error('Error toggling push notifications:', error);
            throw error;
        }
    }

    /**
     * Send test push notification
     */
    async sendTestNotification() {
        try {
            const response = await fetch('/push-notifications/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                console.log('Test notification sent successfully');
                return true;
            } else {
                console.error('Failed to send test notification:', data.message);
                return false;
            }
        } catch (error) {
            console.error('Error sending test notification:', error);
            throw error;
        }
    }

    /**
     * Get push notification status
     */
    async getPushNotificationStatus() {
        try {
            const response = await fetch('/push-notifications/status', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                return {
                    enabled: data.enabled,
                    subscribed: data.subscribed,
                    lastNotification: data.last_notification
                };
            } else {
                console.error('Failed to get push notification status:', data.message);
                return null;
            }
        } catch (error) {
            console.error('Error getting push notification status:', error);
            throw error;
        }
    }

    /**
     * Convert URL base64 to Uint8Array
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    /**
     * Get public key (placeholder - should be configured)
     */
    getPublicKey() {
        // This should be configured in your environment
        // For now, we'll use a placeholder
        return 'BElmZWFzdHJlYWQgdGhpcyBpcyBhIHBsYWNlaG9sZGVyIGZvciBhIHZhbGlkIFZBUEkgcHVibGljIGtleQ==';
    }
}

// Initialize push notifications when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.pushNotificationManager = new PushNotificationManager();
});
