import React from "react";
import {
    SupportedThemes,
    useCurrentThemeContext,
} from "../contexts/CurrentThemeContext";
import { TogglePill } from "./TogglePill";

export const ThemeToggle: React.FC = () => {
    const { currentTheme, setCurrentTheme } = useCurrentThemeContext();

    return (
        <TogglePill
            initialState={currentTheme == SupportedThemes.dark}
            toggleFunc={(event) =>
                setCurrentTheme(
                    event.target.checked
                        ? SupportedThemes.dark
                        : SupportedThemes.light
                )
            }
        />
    );
};
