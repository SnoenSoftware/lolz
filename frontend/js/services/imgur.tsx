import { render } from "react-dom";
import { ImgurAlbum, ImgurLol } from "../components/ImgurLol";

export interface IImgurImageResponse {
    link: string;
}

export interface IImgurAlbumResponse {
    images: IImgurImageResponse[];
}

type ImgurResponse = IImgurImageResponse | IImgurAlbumResponse;

const isImgurImage = (a: any): a is IImgurImageResponse => a && !a.images;

const isImgurAlbum = (a: any): a is IImgurAlbumResponse => a && a.images;

const fetchImgurData = async (imgurUrl: string): Promise<ImgurResponse> => {
    let apiUrl = "/api/imgur";
    return fetch(apiUrl + "?url=" + imgurUrl, {
        method: "GET",
        mode: "no-cors",
        headers: {
            Accept: "application/json",
        },
    })
        .then((data) => data.json())
        .then((data) => data.data);
};

export const loadImgur = async () => {
    let imgurs = document.querySelectorAll<HTMLElement>("imgur");
    Array.from(imgurs)
        .filter((elem) => elem.dataset.visited !== "true")
        .forEach(async (elem) => {
            let imgurUrl = elem.dataset.url;
            const data = await fetchImgurData(imgurUrl);

            if (isImgurImage(data)) {
                render(<ImgurLol {...data} />, elem);
            } else if (isImgurAlbum(data)) {
                render(<ImgurAlbum {...data} />, elem);
            }
            elem.dataset.visited = "true";
        });
};

export default loadImgur;
