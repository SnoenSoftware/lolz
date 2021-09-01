import { createGlobalStyle } from 'styled-components';
import { SupportedThemes } from '../contexts/CurrentThemeContext';

export const GlobalStyle = createGlobalStyle<{ currentTheme: SupportedThemes }>`
    #app {
        width: 100%;
    }
    
    body {
        background-color: ${(props) =>
            props.currentTheme == SupportedThemes.dark ? '#121212' : 'white'};
        color: ${(props) =>
            props.currentTheme == SupportedThemes.dark ? '#ddd' : 'black'};
    }

    .embedly-card iframe {
        background-color: white;
    }
`;
