(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

console.log('[SERVICE WORKER] Starting...');

self.addEventListener('push', function (event) {
    console.log('[SERVICE WORKER] Received push event');

    var data = event.data.json(),
        title = data.title,
        message = data.message,
        icon = data.icon;
    delete data.title;
    delete data.message;
    delete data.icon;

    console.log('[SERVICE WORKER] About to show notification');

    event.waitUntil(self.registration.showNotification(title, {
        body: message,
        icon: icon,
        // badge: 'images/badge.png',
        data: data
    }));

    console.log('[SERVICE WORKER] Notification shown.');
});

self.addEventListener('notificationclick', function (event) {
    console.log('[SERVICE WORKER] Received notification clicked event');

    var data = event.notification.data;
    event.notification.close();

    console.log('[SERVICE WORKER] Closed notification');

    if (data && data.url) {
        console.log('[SERVICE WORKET] About to open URL...');

        event.waitUntil(clients.openWindow(data.url));

        console.log('[SERVICE WORKER] URL opened.');
    }
});

console.log('[SERVICE WORKER] Started.');

},{}]},{},[1]);

//# sourceMappingURL=service-worker.js.map
