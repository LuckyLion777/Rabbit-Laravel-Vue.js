'use strict';

import mkApiMethods from './methods.js'
import selectAvailableStore from './store/select.js'
import StorageStore from './store/localstorage.js'
import ObjectStore from './store/object.js'

window.api = new (function() {
    let
        store = selectAvailableStore(new StorageStore(), new ObjectStore()),
        isConnected = true, tainted = false,
        hasQueue = () => {
            if (!store.has('queue_barntrax')) {
                return false;
            }
            return window.user_id in store.get('queue_barntrax');
        },
        getQueue = () => store.get('queue_barntrax')[window.user_id || 0],
        setQueue = queue => {
            if (store.has('queue_barntrax')) {
                const queues = store.get('queue_barntrax');
                queues[window.user_id || 0] = queue;
                store.set('queue_barntrax', queues);
            } else {
                store.set('queue_barntrax', { [window.user_id || 0]: queue });
            }
        },
        disconnected = () => {
            if (!isConnected) return;
            isConnected = false;
            $('#callout-offline').fadeIn();
        },
        connected = () => {
            if (tainted && !getQueue().length) {
                tainted = false;
                if (confirm('The synchronization after your offline work has finished. ' +
                        'You might need to refresh the page to see changes. \n' +
                        'Do you want to refresh now?')) {
                    window.location.reload();
                }
            }
            if (isConnected) return;
            isConnected = true;
            $('#callout-offline').fadeOut();
        },
        clearStore = () => {
            // we cannot erase the queue, because
            const queues = store.has('queue_barntrax') ? store.get('queue_barntrax') : {};
            store.clear();
            store.set('queue_barntrax', queues);
        },
        renderQueue = () => {
            $('#callout-offline .item').remove();
            const queue = getQueue();
            if (queue.length) {
                $('#callout-offline .queue-title').show();
                for (const item of queue) {
                    $('#callout-offline .queue').append($('<li class="item">' + item.title + '</li>'));
                }
            } else {
                $('#callout-offline .queue-title').hide();
            }
        },
        enqueue = (method, args, title) => {
            // console.warn('Enqueued action method', method, args);
            tainted = true;
            const queue = getQueue();
            if (title instanceof Function) {
                title = title(...args);
            }
            queue.push({ method, arguments: args, title });
            setQueue(queue);
            renderQueue();
        },
        handleQueueItem = function (method, args, title) {
            // console.info('Dequeue action method', method, args);
            methods[method].apply(this, args).catch(
                function (response) {
                    if (!response.status) {
                        // back into the queue if internet is diconnected again
                        enqueue(method, args, title);
                    }
                }
            );
        },
        handleQueue = () => {
            const queue = getQueue();
            if (!queue.length) return;
            setQueue([]);
            renderQueue();
            for (const item of queue) {
                handleQueueItem(item.method, item.arguments, item.title);
            }
        },
        getCacheKey = (method, args) => 'cache_' + method + '_' + Sha1.hash(JSON.stringify([method, args])),
        storeCache = (method, args, result) => {
            // console.info('Caching method result', method, args, result);
            store.set(getCacheKey(method, args), result);
        },
        hasCache = (method, args) => {
            var key = getCacheKey(method, args);
            if (store.has(key)) {
                // console.info('Cached result is available', method, args, store.get(key));
                return true;
            } else {
                // console.warn('Cached result is missing', method, args);
                return false;
            }
        },
        getCache = (method, args) => store.get(getCacheKey(method, args)),
        mkVerbAction = verb =>  (...args) => Vue.http[verb](...args).then(response => response.data),
        methods = mkApiMethods(...['get', 'post', 'put', 'delete'].map(mkVerbAction));

    // clear data from another user
    // ideally, we should do this on logout
    if (store.get('__user_id') !== window.user_id) {
        clearStore();
        store.set('__user_id', window.user_id);
    }

    if (!hasQueue()) {
        setQueue([]);
    }
    if (getQueue().length) {
        tainted = true;
    }

    for (const method of Object.keys(methods)) {
        this[method] = (...args) => {
            const func = methods[method],
                JSON = window.JSON;
            return func(...args).then(
                data => {
                    connected();
                    handleQueue();
                    if (func.cached) {
                        storeCache(method, args, data);
                    }
                    return data;
                },
                response => {
                    if (response.status) {
                        // Not an internet problem
                        connected();
                        handleQueue();
                        return Promise.reject(response);
                    }
                    disconnected();
                    if (func.queued) {
                        enqueue(method, args, func.queued);
                    }
                    if (func.cached && hasCache(method, args)) {
                        return getCache(method, args);
                    }
                    if ('default' in func) {
                        return JSON.parse(JSON.stringify(
                            func.default instanceof Function ? func.default(...args) : func.default
                        ));
                    }
                    return Promise.reject(response);
                }
            );
        };
    }
})();
