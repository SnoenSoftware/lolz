import React, { useEffect, useState } from "react";
import getMoreLolz, { IServerLol } from "../services/lolz-api";
import Lol from "./Lol";
import renderReddits from "../reddit";
import loadImgur from "../imgur";
import renderTweets from "../twitter";
import { saveLolAsViewed, seen } from "../services/viewedDb";

const LolzList = () => {
    const [page, setPage] = useState(0);
    const [lolz, setLolz] = useState<IServerLol[]>([]);
    const [initialLoadDone, setInitialLoadDone] = useState(false);

    const unloader = async (url: string) => {
        const lol = lolz.find((data: IServerLol) => data.url == url);
        setLolz(lolz.filter((lol) => lol.url != url));
        if (!(await seen(lol))) {
            await saveLolAsViewed(lol);
        }
    };

    useEffect(() => {
        getMoreLolz()
            .then((unseenLolz) => setLolz(lolz.concat(unseenLolz)))
            .then(() => setInitialLoadDone(true))
            .then(() => {
                renderReddits();
                loadImgur();
                renderTweets();
            });
    }, [page]);

    useEffect(() => {
        if (lolz.length < 5 && initialLoadDone) {
            setPage(page + 1);
        }
    }, [lolz]);

    return (
        <div className={"lolz-wrapper"}>
            {lolz.map((data: IServerLol) => (
                <Lol lolData={data} unloadCallback={unloader} key={data.url} />
            ))}
        </div>
    );
};

export default LolzList;
