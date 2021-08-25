import { useRef, useState, useEffect, MutableRefObject } from 'react';
import { FC } from 'react';
import { IServerLol } from '../services/lolz-api';
import styled from 'styled-components';

interface LolProps {
    lolData: IServerLol;
    unloadCallback(url: string): any;
}

const LolContainer = styled.div`
    margin: auto;
    text-align: center;
    max-width: 95vw;
    @media screen and (max-width: 880px) {
        min-width: 95vw;
    }
    img {
        width: 90%;
        cursor: pointer;
    }
    @media screen and (min-width: 1024px) {
        max-width: 70vw;
        padding: 2rem;
        img {
            max-width: 30vw;
        }
    }
    &:after {
        position: relative;
        content: '';
        height: 2px;
        background-image: linear-gradient(
            to right,
            rgba(46, 138, 253, 0),
            rgba(46, 138, 253, 0.75),
            rgba(46, 138, 253, 0)
        );
        min-width: 40vw;
        margin-top: 2rem;
        border-radius: 3px;
        z-index: 2;
        display: block;
    }
`;

const LolHeader = styled.h2`
    cursor: pointer;
`;

const Lol: FC<LolProps> = ({ lolData, unloadCallback }) => {
    const { url, title, content } = lolData;
    const refBox = useRef();
    const [hasBeenOnScreen, setHasBeenOnScreen] = useState(false);

    const useOnScreen = (ref: MutableRefObject<any>) => {
        const [isIntersecting, setIntersecting] = useState(false);

        const observer = new IntersectionObserver(([entry]) =>
            setIntersecting(entry.isIntersecting)
        );

        useEffect(() => {
            observer.observe(ref.current);
            // Remove the observer as soon as the component is unmounted
            return () => {
                observer.disconnect();
            };
        }, []);

        return isIntersecting;
    };

    const clickHandler = () => {
        window.open(url, '_blank');
    };

    const isOnScreen = useOnScreen(refBox);

    useEffect(() => {
        if (hasBeenOnScreen && !isOnScreen) {
            unloadCallback(lolData.url);
        } else if (isOnScreen) {
            setHasBeenOnScreen(true);
        }
    }, [isOnScreen]);

    return (
        <LolContainer ref={refBox} data-url={url} onClick={clickHandler}>
            <LolHeader>{title}</LolHeader>
            <div dangerouslySetInnerHTML={{ __html: content }} />
        </LolContainer>
    );
};

export default Lol;
