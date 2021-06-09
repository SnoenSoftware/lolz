import { Configuration } from "webpack";
import RemovePlugin from "remove-files-webpack-plugin";
import { merge } from 'webpack-merge';
import { coreConfig } from "./webpack.core.conf";

export const config: Configuration = merge(coreConfig,{
    devtool: "eval-source-map",
    mode: "development",
    plugins: [new RemovePlugin({
        watch: {
            include: [
                "./public/build/main.css"
            ]
        },
        before: {
            include: [
                "./public/build/main.css"
            ]
        }
    })]
});

export default config;