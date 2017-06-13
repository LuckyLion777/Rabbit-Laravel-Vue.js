'use strict';

export default (...stores) => stores.find(store => store.isAvailable());
