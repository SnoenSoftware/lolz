import {loadImgur} from "./imgur";
import {renderReddits} from "./reddit";
import renderTweets from "./twitter";

const ajaxRender = () => {
    loadImgur();
    renderReddits();
    renderTweets();
    window.onYouTubePlayerAPIReady();
};

export default ajaxRender;