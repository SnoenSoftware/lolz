(function(){
    document.addEventListener('click', (event) => {
        let lol = event.target.closest('.lol');
        if (null === lol) {
            return;
        }
        let url = lol.dataset.url;
        if (url) {
            window.open(url, '_blank');
        }
    });
})();
