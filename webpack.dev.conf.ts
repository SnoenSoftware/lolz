import * as webpack from "webpack";
import * as path from "path";
const RemovePlugin = require('remove-files-webpack-plugin');

const config: webpack.Configuration = {
    devtool: "eval-source-map",
    mode: "development",
    entry: "./frontend/js/app.tsx",
    resolve: {
        // Add '.ts' and '.tsx' as resolvable extensions.
        extensions: [".ts", ".tsx", ".js", ".json"]
    },
    output: {
        path: path.resolve(__dirname, "public/build"),
        filename: "app.js"
    },
    module: {
        rules: [
            // All files with a '.ts' or '.tsx' extension will be handled by 'awesome-typescript-loader'.
            { test: /\.tsx?$/, loader: "awesome-typescript-loader" },
            {
                test: /\.s[ac]ss?$/,
                use: [
                    "style-loader",
                    "css-loader",
                    "sass-loader"
                ]
            }
        ]
    },
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
};

export default config;