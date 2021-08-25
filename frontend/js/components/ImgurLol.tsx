import React from "react";
import styled from "styled-components";
import { IImgurAlbumResponse, IImgurImageResponse } from "../services/imgur";

const ImgurAlbumWrapper = styled.div`
    display: flex;
    flex-direction: column;
    img {
        margin: auto;
    }
`;

export const ImgurLol: React.FC<IImgurImageResponse> = ({ link }) => {
    return <img src={link} alt={"Some imgur funny"} />;
};

export const ImgurAlbum: React.FC<IImgurAlbumResponse> = ({ images }) => {
    return (
        <ImgurAlbumWrapper>
            {images.map((image) => (
                <ImgurLol link={image.link} />
            ))}
        </ImgurAlbumWrapper>
    );
};
