const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .copy('resources/images', 'public/images')
    .webpackConfig({
        module: {
            rules: [
                {
                    test: /\.tsx?$/,
                    exclude: [
                        /node_modules/
                    ],
                    use: {
                        loader: "ts-loader",
                        options: {
                            appendTsSuffixTo: [/\.vue$/]
                        }
                    }
                },
                // {
                //     test: /\.vue$/,
                //     use: {
                //         loader: "vue-loader"
                //     }
                // }
            ]
        },
        resolve: {
            extensions: ["*", ".js", ".jsx", ".vue", ".ts", ".tsx"]
        }
    });
