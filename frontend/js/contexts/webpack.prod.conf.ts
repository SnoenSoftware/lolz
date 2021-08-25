import { Configuration } from "webpack";

import { merge } from "webpack-merge";
import { coreConfig } from "./webpack.core.conf";

const config: Configuration = merge(coreConfig, {
    mode: "production",
    entry: "./frontend/js/index.tsx",
});

export default config;
