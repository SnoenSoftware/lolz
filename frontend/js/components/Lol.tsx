import React, { useRef, useState, useEffect, MutableRefObject } from "react";
import { IServerLol } from "../services/lolz-api";

interface LolProps {
    lolData: IServerLol;
    unloadCallback(url: string): any;
}

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

const clickHandler = (loldata: IServerLol) => {
    let url = loldata.url;
    if (url) {
        window.open(url, "_blank");
    }
};

const Lol = (props: LolProps) => {
    const refBox = useRef();
    const isOnScreen = useOnScreen(refBox);
    const [hasBeenOnScreen, setHasBeenOnScreen] = useState(false);

    useEffect(() => {
        if (hasBeenOnScreen && !isOnScreen) {
            props.unloadCallback(props.lolData.url);
        } else if (isOnScreen) {
            setHasBeenOnScreen(true);
        }
    }, [isOnScreen]);

    return (
        <div
            ref={refBox}
            className={"lol"}
            data-url={props.lolData.url}
            onClick={() => clickHandler(props.lolData)}
        >
            <h2>{props.lolData.title}</h2>
            <div dangerouslySetInnerHTML={{ __html: props.lolData.content }} />
        </div>
    );
};

export default Lol;
