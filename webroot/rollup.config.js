import { nodeResolve } from '@rollup/plugin-node-resolve';
import commonjs from '@rollup/plugin-commonjs';
import babel from '@rollup/plugin-babel';
import { terser } from "rollup-plugin-terser";
import json from '@rollup/plugin-json';

const production = !process.env.ROLLUP_WATCH, configurations = [];

// add static script - 'old way'
configurations.push({
    input: 'js/static/script.js',
    output: {
        sourcemap: false,
        dir: 'js',
        entryFileNames: production ? 'script.[hash].min.js' : 'script.min.js',
        inlineDynamicImports: true,
    },
    treeshake: false,
    plugins: [
        nodeResolve({
            browser: true
        }),
        json(),
        terser({
            warnings: false,
            parse: {},
            compress: {},
            mangle: false,
            module: false,
        })
    ]
});

// add an 'app' to the config
configurations.push({
    input: 'js/src/main.app.js',
    output: {
        name: 'main',
        sourcemap: false,
        dir: 'js',
        entryFileNames: production ? '[name].[hash].min.js' : '[name].min.js',
        format: 'umd'
    },
    plugins: [
        nodeResolve({
            browser: true
        }),
        json(),
        commonjs(),
        production ? babel({babelHelpers: 'bundled', exclude: 'node_modules/**'}) : null,
        production ? terser() : null
    ]
});

export default configurations;
