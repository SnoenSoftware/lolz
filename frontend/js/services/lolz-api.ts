import { seen } from './viewedDb';

interface IServerDate {
    date: string;
    timezone_type: number;
    timezone: string;
}

interface IServerLol {
    content: string;
    url: string;
    fetched: IServerDate;
    imageUrl: string;
    videoSources: string[];
    caption: string | null;
    title: string;
}

const onlyUnseen = async (toBeFiltered: IServerLol[]) => {
    const unseenMap = await Promise.all(
        toBeFiltered.map(async (lol) => {
            return !(await seen(lol));
        })
    );
    return toBeFiltered.filter((lol, index) => unseenMap[index]);
};

let page = 0;

const getMoreLolz = async (): Promise<IServerLol[]> => {
    const response = await fetch(`/api/more/page/${page}`);
    const data: IServerLol[] = await response.json();

    page++;
    const unseen = await onlyUnseen(data);
    if (unseen.length >= 5 || data.length === 0) {
        return unseen;
    } else {
        return unseen.concat(await getMoreLolz());
    }
};

export default getMoreLolz;
export { IServerLol, IServerDate };
