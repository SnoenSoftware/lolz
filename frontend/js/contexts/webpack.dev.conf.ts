import { Configuration } from "webpack";
import ReactRefreshPlugin from "@pmmmwh/react-refresh-webpack-plugin";
import BabelRefreshPlugin from "react-refresh/babel";
import { mergeWithRules } from "webpack-merge";
import { WebpackPluginServe } from "webpack-plugin-serve";
import { coreConfig } from "./webpack.core.conf";

export const config: Configuration = mergeWithRules({
    module: {
        rules: {
            test: "match",
            options: "replace",
        },
    },
})(coreConfig, {
    devtool: "eval-source-map",
    entry: ["webpack-plugin-serve/client", "./frontend/js/index.tsx"],
    mode: "development",
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                loader: "babel-loader",
                options: {
                    plugins: [BabelRefreshPlugin],
                },
            },
        ],
    },
    plugins: [new ReactRefreshPlugin(), new WebpackPluginServe()],
    watch: true,
});

export default config;
