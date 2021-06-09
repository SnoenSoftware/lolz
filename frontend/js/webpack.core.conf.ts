import { Configuration } from "webpack";
import { resolve } from "path";
import MiniCssExtractPlugin from 'mini-css-extract-plugin';

export const coreConfig: Configuration = {
    entry: "./frontend/js/app.tsx",
    resolve: {
        // Add '.ts' and '.tsx' as resolvable extensions.
        extensions: [".ts", ".tsx", ".js", ".json"]
    },
    output: {
        path: resolve(__dirname, "../../public/build"),
        filename: "app.js"
    },
    module: {
        rules: [
            { test: /\.tsx?$/, loader: "babel-loader" },
            {
                test: /\.s[ac]ss?$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    "css-loader",
                    "sass-loader"
                ]
            }
        ],
    },
    plugins: [new MiniCssExtractPlugin()]
};