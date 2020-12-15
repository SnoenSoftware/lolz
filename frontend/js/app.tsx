import LolzList from "./components/LolzList";
import React from "react";
import ReactDOM from "react-dom";
import "../css/app.scss";

window.scrollTo(0, 0);

ReactDOM.render(
    <React.StrictMode>
        <LolzList />
    </React.StrictMode>,
    document.getElementById("app")
);
