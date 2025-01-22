import { createGlobalStyle } from 'styled-components';
import { SupportedThemes } from '../contexts/CurrentThemeContext';

export const GlobalStyle = createGlobalStyle<{ currentTheme: SupportedThemes }>`
    #app {
        width: 100%;
    }
    
    body {
        background-color: white;
        color: black;
        @media (prefers-color-scheme: dark) {
            background-color: #121212;
            color: #ddd;
        }
    }

    .embedly-card iframe {
        background-color: white;
    }
`;
