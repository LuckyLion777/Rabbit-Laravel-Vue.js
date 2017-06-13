const urlB64ToUint8Array = base64String => {
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
}, bufferToB64 = buffer => {
    return btoa(String.fromCharCode.apply(null, new Uint8Array(buffer)));
};

const WebPushNotificationManager = function() {};

// Functions required for any notifications manager

WebPushNotificationManager.prototype.isAvailable = function() {
    return Promise.resolve('serviceWorker' in navigator && 'PushManager' in window);
};

WebPushNotificationManager.prototype.isDisabled = function () {
    return this.getServiceWorker().then(
        swReg => swReg.pushManager.permissionState({ userVisibleOnly: true })
                    .then(state => state === 'denied')
    );
};

WebPushNotificationManager.prototype.isSubscribed = function() {
    return this.getSubscription().then(sub => !!sub);
};

WebPushNotificationManager.prototype.subscribe = function() {
    return this.getSubscription().then(sub => {
        if (sub) {
            this.saveCredentials(sub, false);
        } else {
            return this.reSubscribe().then(sub => {
                console.log('ReSubscribed, saving credentials...', sub);
                this.saveCredentials(sub, true).then(() => {
                    console.log('saved new credentials');
                });
            });
        }
    })
};

WebPushNotificationManager.prototype.unsubscribe = function() {
    return this.getSubscription().then(sub => {
        if (!sub) return;
        return sub.unsubscribe();
    })
};

WebPushNotificationManager.prototype.updateServer = function() {
    return this.getSubscription().then(sub => {
        if (sub) {
            this.saveCredentials(sub, false)
        }
    }, () => {});
};

// Functions specific to this service

WebPushNotificationManager.prototype.saveCredentials = function (sub, reset) {
    let publicKey = null, authToken = null;
    if (typeof sub.getKey === 'function') {
        // At least Firefox documentation claims this method is specific to them,
        // but Chrome also has it
        console.log('PushSubscription::getKey is available');
        publicKey = bufferToB64(sub.getKey('p256dh'));
        authToken = bufferToB64(sub.getKey('auth'));
    }
    return api.saveWebPushEndpoint(sub.endpoint, reset, publicKey, authToken);
};

WebPushNotificationManager.prototype.getServiceWorker = function () {
    if (!this.isAvailable()) return Promise.reject('service_workers_not_available');

    return navigator.serviceWorker.register('service-worker.js?v2');
};

WebPushNotificationManager.prototype.getSubscription = function () {
    return this.getServiceWorker().then(swReg => {
        return swReg.pushManager.getSubscription();
    });
};

WebPushNotificationManager.prototype.reSubscribe = function() {
    return this.getServiceWorker().then(swReg => {
        console.log('About to subscribe', window.web_push_public, urlB64ToUint8Array(window.web_push_public), '<<');
        return swReg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlB64ToUint8Array(window.web_push_public)
        }).then(
            sub => {
                console.log('Successfully subscribed', sub, sub.endpoint, '<<');
                swReg.showNotification('Hutch Notifications Enabled!', {
                    body: 'Thanks for subscribing to Hutch Notifications.',
                    icon: 'img/favicon/favicon-96x96.png',
                });
                return sub;
            },
            err => {
                console.log('Error while trying to subscribe: ', err);
                return Promise.reject(err);
            }
        );
    });
};

// Export the manager

window.WebPushNotificationManager = new WebPushNotificationManager();
