const loadImgur = () => {
    let apiUrl = "/api/imgur";
    let imgurs = document.querySelectorAll("imgur");
    Array.from(imgurs).forEach(function (elem) {
        let imgurUrl = elem.dataset.url;
        fetch(apiUrl + "?url=" + imgurUrl, {
            method: "GET",
            mode: "cors",
            headers: {
                Accept: "application/json",
            },
        })
            .then((data) => data.json())
            .then((data) => data.data)
            .then((data) => {
                // We could be getting an album or an image back, only way to know is to check structure
                let newNode;
                if (!data.hasOwnProperty("images")) {
                    newNode = document.createElement("img");
                    newNode.setAttribute("src", data.link);
                } else {
                    newNode = document.createElement("div");
                    newNode.classList.add("imgur-album");
                    data.images.forEach((image) => {
                        let imgNode = document.createElement("img");
                        imgNode.setAttribute("src", image.link);
                        newNode.append(imgNode);
                    });
                }
                elem.replaceWith(newNode);
            });
    });
};

export default loadImgur;
