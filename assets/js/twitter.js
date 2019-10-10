(function(){
    let oembedUrl = '/api/twitter';
    let tweets = document.querySelectorAll('tweet');
    Array.from(tweets).forEach(function (elem) {
        let tweetUrl = elem.dataset.url;
        fetch(
            oembedUrl + '?url=' + tweetUrl,
            {
                method: 'GET',
                mode: "cors",
                headers: {
                    "Accept": "application/json"
                }
            }
        )
            .then(data => data.json())
            .then(data => {
                let newNode = document.createElement('div');
                newNode.innerHTML = data.html;
                newNode.querySelector('script').remove();
                elem.replaceWith(newNode);
                twttr.widgets.load();
            });
    })
})();