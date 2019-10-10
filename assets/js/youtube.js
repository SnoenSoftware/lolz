(function(){
    window.onYouTubePlayerAPIReady = function() {
        let videos = document.querySelectorAll('youtube');
        debugger;
        Array.from(videos).forEach(function (elem) {
            let videoId = elem.dataset.viewId;
            elem.setAttribute('id', videoId);
            let player = new YT.Player(videoId, {
                height: 360,
                width: 640,
                videoId: videoId
            });
        })
    }
})();