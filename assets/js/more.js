import {ifLolIsUnseen} from "./viewedDb";

const getMoreLolz = (page) => {
    return fetch(`/api/more/page/${page}`);
};

const loadMoreLolz = () => {
    window.currentPage = window.currentPage || 0;
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
        if (document.querySelectorAll('.lol').length < 5 && lolz.length === 30) {
            return loadMoreLolz();
        } else if (document.querySelectorAll('.lol').length === 0) {
            let wrapper = document.createElement('div');
            wrapper.classList.add('eof');
            let newElement = document.createElement('h1');
            newElement.innerText = "No more lolz to give, come back later";
            wrapper.append(newElement);
            document.querySelector('body').append(wrapper);
        }
    });
    window.currentPage += 1;
    return promise;
};

export default loadMoreLolz;