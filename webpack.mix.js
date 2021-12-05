const mix = require('laravel-mix');
const path = require('path');

mix.config.webpackConfig.output = {
    chunkFilename: '[name].bundle.js',
    path: path.resolve(__dirname, 'resources/assets'),
    // publicPath: 'resources/assets'
}


mix.js('resources/ui/pier-editor/index.js', 'js/pier-editor.js')
    .js('resources/ui/pier-cms/index.js', 'js/pier-cms.js')
    .js('resources/ui/pier-form/index.js', 'js/pier-form.js');;
