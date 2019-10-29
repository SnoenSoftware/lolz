require('./viewedDb');
import loadMoreLolz from "./more";
import {saveLolAsViewed, ifLolHasBeenSeen} from "./viewedDb";

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
        Array.from(lolz).forEach(lol => {
            ifLolHasBeenSeen(lol, () => {lol.remove()});
        });
    };

    document.addEventListener('scroll', () => {
        refreshInViewClasses();
        hideOutOfViewLolz();
    });

    document.addEventListener('wheel', (event) => {
        if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight && event.deltaY > 0) {
            Array.from(document.querySelectorAll('.lol')).forEach((lol) => {
                saveLolAsViewed(lol);
            });
        }
    });

    window.mutationListener = new MutationObserver(() => {
        if (document.querySelectorAll('.lol').length === 0) {
            loadMoreLolz().then(() => removeAlreadySeenLolz());
        }
    });

    window.mutationListener.observe(document.querySelector('.lolz-wrapper'), {
        childList: true
    });

    refreshInViewClasses();
    removeAlreadySeenLolz();
})();