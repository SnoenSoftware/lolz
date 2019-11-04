import loadMoreLolz from "./more";
import {saveLolAsViewed, ifLolHasBeenSeen} from "./viewedDb";
import ajaxRender from "./ajaxRender";

window.scrollEventBeingHandled = false;
window.lastScrollTop = 0;

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

    document.addEventListener('scroll', (event) => {
        if (window.scrollEventBeingHandled) {
            return;
        }
        window.scrollEventBeingHandled = true;
        let st = window.pageYOffset || document.documentElement.scrollTop;
        if (st < window.lastScrollTop){
            window.scrollEventBeingHandled = false;
            return; // Do not handle scrolling upwards
        }
        window.lastScrollTop = st <= 0 ? 0 : st;
        refreshInViewClasses();
        hideOutOfViewLolz();
        let visibleLolz = document.querySelectorAll('.lol').length;
        if (visibleLolz <= 10) {
            loadMoreLolz().then(() => {
                ajaxRender();
            }).finally(() => {window.scrollEventBeingHandled = false});
        } else {
            window.scrollEventBeingHandled = false;
        }
    });

    document.addEventListener('wheel', (event) => {
        if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight && event.deltaY > 0) {
            Array.from(document.querySelectorAll('.lol')).forEach((lol) => {
                saveLolAsViewed(lol);
            });
        }
    });

    refreshInViewClasses();
    removeAlreadySeenLolz();
})();