/**
 * Notification manager which just uses the one available implementation out of provided list.
 * All of the managers must implement similar interface (public part).
 * @param managers
 * @constructor
 */
const CompositeNotificationManager = function (managers) {
    this._managers = managers;
    this._manager = undefined;
};

// Functions required for any notifications manager (public interface)

CompositeNotificationManager.prototype.isAvailable = function () {
    return this.getManager().then(() => true, () => false);
};

CompositeNotificationManager.prototype.isDisabled = function () {
    return this.getManager().then(manager => manager.isDisabled(), () => false);
};

CompositeNotificationManager.prototype.isSubscribed = function () {
    return this.getManager().then(manager => manager.isSubscribed(), () => false);
};

CompositeNotificationManager.prototype.subscribe = function() {
    return this.getManager().then(manager => manager.subscribe());
};

CompositeNotificationManager.prototype.unsubscribe = function () {
    return this.getManager().then(manager => manager.unsubscribe());
};

CompositeNotificationManager.prototype.updateServer = function () {
    return this.getManager().then(manager => manager.updateServer(), () => {});
};

// Privates

CompositeNotificationManager.prototype.getManager = function() {
    if (typeof this._manager !== 'undefined') {
        return this._manager ? Promise.resolve(this._manager) : Promise.reject();
    }

    return this._managers
        .reduce((promise, manager) => {
            // Let's initiate inquiry before waiting for result of previous
            const managerAvailable = manager.isAvailable();
            // So, check if any of the previous managers are available
            return promise.then(available => {
                // ... and if so, just pass the first available manager
                if (available) {
                    return Promise.resolve(available);
                }
                // Otherwise, check if current manager is available
                return managerAvailable.then(res => {
                    return res ? manager : null;
                })
            });
        }, Promise.resolve(null))
        .then(manager => {
            // Cache the found manager for consistency
            this._manager = manager;
            return manager;
        });
};

CompositeNotificationManager.prototype.reload = function() {
    delete this._manager;
};

// Export

window.NotificationManager = new CompositeNotificationManager([
    // List here all the web notifications implementations
    window.WebPushNotificationManager
]);
