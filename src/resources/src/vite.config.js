import { defineConfig } from 'vite';
import { resolve } from 'path';
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer';

export default defineConfig({

    build: {
        outDir: '../dist',
        emptyOutDir: true,
        assetsInlineLimit: 0,
        rolldownOptions: {
            makeAbsoluteExternalsRelative: true,
            input: {
                frontend: resolve(__dirname, 'js/frontend.js'),
                form: resolve(__dirname, 'js/form.js'),
            },
            output: {
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                },
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name].js',
                assetFileNames: (assetInfo) => {
                    const fileName = assetInfo.names[0];
                    if (/\.(css)$/.test(fileName)) {
                        return 'css/[name][extname]';
                    }
                    if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico|webp)$/i.test(fileName)) {
                        return '[ext]/[name][extname]';
                    }
                    if (/\.(woff|woff2|eot|ttf|otf)$/i.test(fileName)) {
                        return 'fonts/[name][extname]';
                    }
                    return 'assets/[name][extname]';
                },
            },
        },
    },

    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: ['legacy-js-api', 'import', 'color-functions', 'if-function', 'global-builtin'],
            },
        },
    },

    plugins: [

        ViteImageOptimizer({
            includePublic: true,
            svg: {
                multipass: true,
                plugins: [
                    {
                        name: 'preset-default',
                        params: {
                            overrides: {
                                cleanupNumericValues: false,
                                cleanupIds: {
                                    minify: false,
                                    remove: false,
                                },
                                convertPathData: false,
                            },
                        },
                    },
                    'sortAttrs',
                    {
                        name: 'addAttributesToSVGElement',
                        params: {
                            attributes: [{ xmlns: 'http://www.w3.org/2000/svg' }],
                        },
                    },
                ],
            },
            png: {
                quality: 100,
            },
            jpeg: {
                quality: 100,
            },
            jpg: {
                quality: 100,
            },
            webp: {
                quality: 100,
            },
        }),
    ],

});
