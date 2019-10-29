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

const getDbPromise = () => {
    return openDB('viewedDb', 1);
};

const saveLolAsViewed = (lol) => {
    let url = lol.dataset.url;
    let viewedTime = Date.now();
    let dbPromise = getDbPromise();
    dbPromise.then((db) => {
        let transaction = db.transaction('viewed', 'readwrite');
        let store = transaction.objectStore('viewed');
        store.add({
            lolurl: url,
            viewed: viewedTime
        })
    });
};

const runCallbackOnLolIfSeenOrNot = (lol, seenOrNot, callback) => {
    let dbPromise = getDbPromise();
    dbPromise.then((db) => {
        let transaction = db.transaction('viewed', 'readonly');
        let store = transaction.objectStore('viewed');
        let url = lol.dataset ? lol.dataset.url : lol.url;
        store.get(url).then(val => {
            val = seenOrNot ? val : !val;
            if (val) {
                callback(lol);
            }
        });
    });
};

const ifLolHasBeenSeen = (lol, callback) => {
    runCallbackOnLolIfSeenOrNot(lol, true, callback);
};

const ifLolIsUnseen = (lol, callback) => {
    runCallbackOnLolIfSeenOrNot(lol, false, callback);
};

export {saveLolAsViewed, ifLolHasBeenSeen, ifLolIsUnseen};
