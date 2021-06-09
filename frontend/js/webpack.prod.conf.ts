import {Configuration} from "webpack";

import MiniCssExtractPlugin from 'mini-css-extract-plugin';
import { merge } from "webpack-merge";
import { coreConfig } from "./webpack.core.conf";

const config: Configuration = merge(coreConfig, {
    mode: "production",
    entry: "./frontend/js/app.tsx",
});

export default config;