(function () {
    window.onYouTubePlayerAPIReady = function () {
        const videos = document.querySelectorAll('youtube');
        Array.from(videos).forEach(function (elem) {
            const videoId = elem.dataset.viewId;
            elem.setAttribute('id', videoId);
            // eslint-disable-next-line
            const player = new window.YT.Player(videoId, {
                height: 360,
                width: 640,
                videoId: videoId,
            });
        });
    };
})();
