import { Configuration } from 'webpack';
import { resolve } from 'path';

export const coreConfig: Configuration = {
    entry: resolve(__dirname, '../js/index.tsx'),
    resolve: {
        // Add '.ts' and '.tsx' as resolvable extensions.
        extensions: ['.ts', '.tsx', '.js', '.json'],
    },
    output: {
        path: resolve(__dirname, '../../public/build'),
        filename: 'app.js',
    },
    module: {
        rules: [{ test: /\.tsx?$/, loader: 'babel-loader' }],
    },
};
