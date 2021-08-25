import React from "react";
import ReactDOM from "react-dom";
import { CurrentThemeProvider } from "./contexts/CurrentThemeContext";
import { App } from "./components/App";

window.scrollTo(0, 0);

ReactDOM.render(
    <React.StrictMode>
        <CurrentThemeProvider>
            <App />
        </CurrentThemeProvider>
    </React.StrictMode>,
    document.getElementById("app")
);
