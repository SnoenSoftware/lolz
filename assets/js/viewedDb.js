require('idb');
import { openDB } from 'idb';

(function () {
    if (!('indexedDB' in window)) {
        console.log('This browser doesn\'t support IndexedDB');
        return;
    }

    let dbPromise = openDB('viewedDb', 1, {
        upgrade(db, oldVersion, newVersion, transaction) {
            if (!db.objectStoreNames.contains('viewed')) {
                console.log('making a new object store');
                db.createObjectStore('viewed', {keyPath: 'lolurl'});
            }
        }
    });
})();