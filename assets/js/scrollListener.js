require('./viewedDb');
import {openDB} from 'idb';

(function(){
    const elementScrolled = (elem) => {
        let rect = elem.getBoundingClientRect();
        let viewHeight = Math.max(document.documentElement.clientHeight, window.innerHeight);
        return !(rect.bottom < 0 || rect.top - viewHeight >= 0);
    };

    const refreshInViewClasses = () => {
        Array.from(document.querySelectorAll('.lol'), elem => {
            if (elementScrolled(elem) && !elem.classList.contains('in-view')) {
                elem.classList.add('in-view');
            }
        });
    };

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

    const hideOutOfViewLolz = () => {
        Array.from(document.querySelectorAll('.in-view'), elem => {
            if (!elementScrolled(elem)) {
                elem.classList.remove('in-view');
                saveLolAsViewed(elem);
                elem.remove();
            }
        });
    };

    const removeAlreadySeenLolz = () => {
        let lolz = document.querySelectorAll('.lol');
        let dbPromise = getDbPromise();
        dbPromise.then((db) => {
            let transaction = db.transaction('viewed', 'readonly');
            let store = transaction.objectStore('viewed');
            Array.from(lolz).forEach(lol => {
                store.get(lol.dataset.url).then(val => {
                    if (val) {
                        lol.remove();
                    }
                });
            });
        });
    };

    document.addEventListener('scroll', event => {
        refreshInViewClasses();
        hideOutOfViewLolz();
    });

    refreshInViewClasses();
    removeAlreadySeenLolz();
})();