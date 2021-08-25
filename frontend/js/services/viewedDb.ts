import { openDB } from 'idb';
import { IServerLol } from './lolz-api';

(function () {
    if (!('indexedDB' in window)) {
        console.log("This browser doesn't support IndexedDB");
        return;
    }

    let dbPromise = openDB('viewedDb', 1, {
        upgrade(db, oldVersion, newVersion, transaction) {
            if (!db.objectStoreNames.contains('viewed')) {
                console.log('making a new object store');
                db.createObjectStore('viewed', { keyPath: 'lolurl' });
            }
        },
    });
})();

const getDb = async () => {
    return await openDB('viewedDb', 1);
};

const saveLolAsViewed = async (lol: IServerLol) => {
    let url = lol.url;
    let viewedTime = Date.now();
    let db = await getDb();
    let transaction = db.transaction('viewed', 'readwrite');
    let store = transaction.objectStore('viewed');
    await store.add({
        lolurl: url,
        viewed: viewedTime,
    });
};

const seen = async (lol: IServerLol) => {
    let db = await getDb();
    let transaction = db.transaction('viewed', 'readonly');
    let store = transaction.objectStore('viewed');
    let url = lol.url;

    let stored = await store.get(url);
    return !!stored;
};

export { seen, saveLolAsViewed };
