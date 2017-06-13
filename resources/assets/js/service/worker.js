console.log('[SERVICE WORKER] Starting...');

self.addEventListener('push', function (event) {
    console.log('[SERVICE WORKER] Received push event');

    const data = event.data.json(),
        title = data.title,
        message = data.message,
        icon = data.icon;
    delete data.title;
    delete data.message;
    delete data.icon;

    console.log('[SERVICE WORKER] About to show notification');

    event.waitUntil(self.registration.showNotification(
        title,
        {
            body: message,
            icon: icon,
            // badge: 'images/badge.png',
            data
        }
    ));

    console.log('[SERVICE WORKER] Notification shown.');
});

self.addEventListener('notificationclick', function (event) {
    console.log('[SERVICE WORKER] Received notification clicked event');

    const data = event.notification.data;
    event.notification.close();

    console.log('[SERVICE WORKER] Closed notification');

    if (data && data.url) {
        console.log('[SERVICE WORKET] About to open URL...');

        event.waitUntil(
            clients.openWindow(data.url)
        );

        console.log('[SERVICE WORKER] URL opened.');
    }
});

console.log('[SERVICE WORKER] Started.');
