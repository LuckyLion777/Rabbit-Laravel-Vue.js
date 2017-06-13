'use strict';

/**
 * Class implementing store interface via a plane object
 * (such a storage will not be persistent)
 */
export default function () {
    const JSON = window.JSON;
    let store;

    const init = () => {
        if (store) return;
        store = {};
    };

    this.isAvailable = () => true;

    this.clear = () => { store = {}; };

    this.set = (key, value) => {
        init();
        store[key] = JSON.stringify(value);
    };

    this.has = key => {
        init();
        return key in store;
    };

    this.get = key => {
        init();
        const serialized = store[key];
        try {
            return JSON.parse(serialized);
        } catch (e) {
            return undefined;
        }
    };

    this.delete = key => {
        init();
        delete store[key];
    }
};
