'use strict';

/**
 * Class implementing store with a user of a browser-provided html5 store implementation,
 * something like localStorage (default) or sessionStorage.
 * The instances of this class (with save arguments) are is fact using a common store
 * @param type
 * @constructor
 */
export default function (type = 'localStorage') {
    const JSON = window.JSON,
        getStore = () => window[type];

    this.isAvailable = () => {
        try {
            var store = getStore();
            store.setItem('__test__', '__test__');
            var test = store.getItem('__test__');
            store.removeItem('__test__');
            return test === '__test__';
        } catch (e) {
            return false;
        }
    };

    this.clear = () => { getStore().clear(); };

    this.set = (key, value) => { getStore().setItem(key, JSON.stringify(value)); };

    this.has = key => {
        const store = getStore();
        return _.range(0, store.length, 1).some(i => store.key(i) === key);
    };

    this.get = key => {
        var serialized = getStore().getItem(key);
        try {
            return JSON.parse(serialized);
        } catch (e) {
            return undefined;
        }
    };

    this.delete = key => { getStore().removeItem(key); };
};
