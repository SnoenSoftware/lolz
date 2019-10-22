const getMoreLolz = (page) => {
    return fetch(`/api/more/page/${page}`);
};

const loadMoreLolz = () => {
    window.currentPage = window.currentPage || 1;
    getMoreLolz(window.currentPage).then(lolz => lolz.json()).then(lolz => {
        lolz.forEach(lol => {
            let wrapper = document.querySelector('.lolz-wrapper');
            let newElement = document.createElement('div');
            newElement.classList.add('lol');
            let header = document.createElement('h2');
            header.innerHTML = lol.title;
            newElement.innerHTML = lol.content;
            newElement.insertBefore(header, newElement.childNodes[0]);
            wrapper.append(newElement);
        });
    });
    window.currentPage += 1;
};

document.addEventListener('scroll', () => {
    let visibleLolz = document.querySelectorAll('.lol').length;
    if (visibleLolz <= 10) {
        loadMoreLolz();
    }
});
