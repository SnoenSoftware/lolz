import { FC } from 'react';
import { useCurrentThemeContext } from '../contexts/CurrentThemeContext';
import { GlobalStyle } from './GlobalTheme';
import LolzList from './LolzList';

export const App: FC = () => {
    const { currentTheme } = useCurrentThemeContext();
    return (
        <>
            <GlobalStyle currentTheme={currentTheme} />
            <LolzList />
        </>
    );
};
