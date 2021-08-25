const renderTweets = () => {
    const oembedUrl = '/api/twitter';
    const tweets = document.querySelectorAll('tweet');
    Array.from(tweets).forEach(function (elem) {
        const tweetUrl = elem.dataset.url;
        fetch(oembedUrl + '?url=' + tweetUrl, {
            method: 'GET',
            mode: 'cors',
            headers: {
                Accept: 'application/json',
            },
        })
            .then((data) => data.json())
            .then((data) => {
                const newNode = document.createElement('div');
                newNode.innerHTML = data.html;
                newNode.querySelector('script').remove();
                elem.replaceWith(newNode);
                window.twttr.widgets.load();
            });
    });
};

export default renderTweets;
