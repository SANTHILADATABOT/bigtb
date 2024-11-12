const path = require('path');
const shell = require('shelljs');
const outputPath = path.resolve(__dirname, 'views/assets/js');
const { VueLoaderPlugin } = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

// Clean up old files before building
shell.rm('-rf', outputPath);
shell.rm('-rf', path.resolve(__dirname, 'views/assets/vendor/wp-hooks/pm-hooks.js'));

// Determine if it's a production build
const isProduction = (process.env.NODE_ENV === 'production');

// Helper function to resolve paths
function resolve(dir) {
    return path.join(__dirname, './views/assets/', dir);
}

function resolveNode(dir){
    console.log("Path - "+ dir +" - ", path.join(__dirname, dir));
    return path.join(__dirname, dir);
}

// Plugins array
const plugins = [
    new VueLoaderPlugin(), // Required for processing Vue files
    new MiniCssExtractPlugin({
        // Ensure unique filenames for CSS files to prevent conflicts
        filename: 'assets/css/[name].css',
        chunkFilename: 'assets/css/[id].css',
    })
];

// Webpack configuration
module.exports = [
    {
        entry: {
            'assets/js/pm': './views/assets/src/start.js',
            'assets/js/library': './views/assets/src/helpers/library.js',
            'assets/js/pmglobal': './views/assets/src/helpers/pmglobal.js',
            'assets/vendor/wp-hooks/pm-hooks': './views/assets/vendor/wp-hooks/wp-hooks.js',
            'assets/vendor/vue-fullscreen/vue-fullscreen.min': './views/assets/vendor/vue-fullscreen/vue-fullscreen.js',
        },

        output: {
            path: path.resolve(__dirname, 'views'),
            filename: '[name].js', // Keep separate output JS files
            publicPath: '',
        },

        resolve: {
            extensions: ['.js', '.vue', '.json', '.css'],
            alias: {
                '@assets': resolve(''),
                '@components': resolve('src/components'),
                '@directives': resolve('src/directives'),
                '@helpers': resolve('src/helpers'),
                '@router': resolve('src/router'),
                '@store': resolve('src/store'),
                '@src': resolve('src/'),
                '@node': resolveNode('node_modules'),
                vue$: path.resolve('./node_modules/vue/dist/vue.esm.js')
                // '@node': path.join(__dirname,'node_modules')
            }
        },

        module: {
            rules: [
                // Vue loader
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        // Use Babel loader for JS inside .vue files
                        loaders: {
                            js: 'babel-loader',
                        }
                    }
                },
                // JS loader for regular .js files
                {
                    test: /\.js$/,
                    loader: 'babel-loader',
                    exclude: /node_modules/,
                    options: {
                        presets: ['@babel/preset-env'],
                    }
                },
                // File loader for images
                {
                    test: /\.(png|jpg|gif|svg)$/,
                    loader: 'file-loader',
                    exclude: /node_modules/,
                    options: {
                        name: '[name].[ext]?[hash]',
                        outputPath: '../css/images/',
                    }
                },
                // Less loader (with MiniCssExtractPlugin for extracting CSS)
                {
                    test: /\.less$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: {
                                importLoaders: 1,
                            },
                        },
                        'less-loader'
                    ]
                },
                // CSS loader (with MiniCssExtractPlugin for extracting CSS)
                {
                    test: /\.css$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: {
                                importLoaders: 1,
                            },
                        },
                    ]
                },
                // Font files loader
                {
                    test: /\.(png|woff|woff2|eot|ttf|svg)$/,
                    use: ['url-loader?limit=100000']
                }
            ]
        },

        plugins: plugins,

        // optimization: {
        //     splitChunks: {
        //         chunks: 'all',  // Ensures CSS is extracted from all chunks, not just main ones
        //     },
        // }
    },
];
