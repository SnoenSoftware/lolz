import constate from "constate";
import { useState } from "react";

export enum SupportedThemes {
    dark,
    light,
}

const useCurrentTheme = () => {
    const [currentTheme, setCurrentTheme] = useState<SupportedThemes>(
        SupportedThemes.dark
    );
    return { currentTheme, setCurrentTheme };
};

export const [CurrentThemeProvider, useCurrentThemeContext] = constate(
    useCurrentTheme
);
