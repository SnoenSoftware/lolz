(function(){
    let apiUrl = '/api/imgur';
    let imgurs = document.querySelectorAll('imgur');
    Array.from(imgurs).forEach(function (elem) {
        let imgurUrl = elem.dataset.url;
        fetch(
            apiUrl + '?url=' + imgurUrl,
            {
                method: 'GET',
                mode: "cors",
                headers: {
                    "Accept": "application/json"
                }
            }
        )
            .then(data => data.json())
            .then(data => data.data)
            .then(data=> {
                let newNode = document.createElement('img');
                newNode.setAttribute('src', data.link)
                elem.replaceWith(newNode);
            });
    })
})();