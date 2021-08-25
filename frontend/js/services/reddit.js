const renderReddits = () => {
    const scriptNode = document.createElement("script");
    Array.from(
        document.querySelectorAll('script[name="redditscript"]')
    ).forEach((elem) => {
        elem.remove();
    });
    scriptNode.setAttribute("async", true);
    scriptNode.setAttribute(
        "src",
        "https://embed.redditmedia.com/widgets/platform.js"
    );
    scriptNode.setAttribute("name", "redditscript");
    document.head.append(scriptNode);
};

export default renderReddits;
