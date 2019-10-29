import {ifLolIsUnseen} from "./viewedDb";

const getMoreLolz = (page) => {
    return fetch(`/api/more/page/${page}`);
};

const loadMoreLolz = () => {
    window.currentPage = window.currentPage || 1;
    let promise = getMoreLolz(window.currentPage).then(lolz => lolz.json()).then(lolz => {
        lolz.forEach(lol => ifLolIsUnseen(lol, () => {
            let wrapper = document.querySelector('.lolz-wrapper');
            let newElement = document.createElement('div');
            newElement.classList.add('lol');
            newElement.dataset.url = lol.url;
            let header = document.createElement('h2');
            header.innerHTML = lol.title;
            newElement.innerHTML = lol.content;
            newElement.insertBefore(header, newElement.childNodes[0]);
            wrapper.append(newElement);
        }));
        if (document.querySelectorAll('lol').length < 5 && lolz.length === 30) {
            loadMoreLolz();
        }
    });
    window.currentPage += 1;
    return promise;
};

document.addEventListener('scroll', () => {
    let visibleLolz = document.querySelectorAll('.lol').length;
    if (visibleLolz <= 10) {
        loadMoreLolz();
    }
});

export default loadMoreLolz;