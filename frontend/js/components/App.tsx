import { FC } from 'react';
import { useCurrentThemeContext } from '../contexts/CurrentThemeContext';
import { GlobalStyle } from './GlobalTheme';
import LolzList from './LolzList';
import { ThemeToggle } from './ThemeToggle';

export const App: FC = () => {
    const { currentTheme } = useCurrentThemeContext();
    return (
        <>
            <GlobalStyle currentTheme={currentTheme} />
            <ThemeToggle />
            <LolzList />
        </>
    );
};
