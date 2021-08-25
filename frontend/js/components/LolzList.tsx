import React, { useEffect, useState } from "react";
import getMoreLolz, { IServerLol } from "../services/lolz-api";
import Lol from "./Lol";
import renderReddits from "../services/reddit";
import loadImgur from "../services/imgur";
import renderTweets from "../services/twitter";
import { saveLolAsViewed, seen } from "../services/viewedDb";
import styled from "styled-components";
import { ThemeToggle } from "./ThemeToggle";
import {
    SupportedThemes,
    useCurrentThemeContext,
} from "../contexts/CurrentThemeContext";

const LolzWrapper = styled.div`
    display: flex;
    flex-direction: column;
    align-content: center;
    margin: 0;
`;

const LolzList = () => {
    const [page, setPage] = useState(0);
    const [lolz, setLolz] = useState<IServerLol[]>([]);
    const [initialLoadDone, setInitialLoadDone] = useState(false);
    const [feedEmpty, setFeedEmpty] = useState(false);
    const { currentTheme } = useCurrentThemeContext();

    const unloader = async (url: string) => {
        const lol = lolz.find((data: IServerLol) => data.url == url);
        setLolz(lolz.filter((lol) => lol.url != url));
        if (!(await seen(lol))) {
            await saveLolAsViewed(lol);
        }
    };

    useEffect(() => {
        getMoreLolz()
            .then((unseenLolz) => {
                setLolz(lolz.concat(unseenLolz));
                unseenLolz.length === 0 && setFeedEmpty(true);
            })
            .then(() => setInitialLoadDone(true))
            .then(() => {
                renderReddits();
                loadImgur().then();
                renderTweets();
            });
    }, [page]);

    useEffect(() => {
        if (lolz.length < 5 && initialLoadDone && !feedEmpty) {
            setPage(page + 1);
        }
    }, [lolz]);

    return (
        <LolzWrapper>
            {lolz.map((data: IServerLol) => (
                <Lol lolData={data} unloadCallback={unloader} key={data.url} />
            ))}
        </LolzWrapper>
    );
};

export default LolzList;
