import { Configuration } from "webpack";

import { merge } from "webpack-merge";
import { coreConfig } from "./core.conf";

const config: Configuration = merge(coreConfig, {
    mode: "production",
});

export default config;
