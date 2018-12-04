const path                = require('path');
const mix                 = require('laravel-mix');
const WebpackChunkHash    = require('webpack-chunk-hash');
const { VueLoaderPlugin } = require('vue-loader');

const source = 'resources';
const public = 'public';

mix.setPublicPath(path.normalize(public));

mix.webpackConfig({
  externals: {
    'jquery': 'jQuery',
    'vue': 'Vue'
  },
  output: { chunkFilename: mix.inProduction() ? 'js/parts/[name].[chunkhash].js' : 'js/parts/[name].js' },
  module: {
    rules: [
      {
        enforce: 'pre',
        test: /\.(vue|js)$/,
        exclude: /(node_modules|bower_components)/,
        loader: 'eslint-loader',
        options: {
          fix: false,
          cache: false,
          formatter: require('eslint-friendly-formatter')
        }
      }
    ]
  },
  plugins: [
    new VueLoaderPlugin(),
    new WebpackChunkHash({algorithm: 'md5'})
  ],
  devServer: { overlay: true },
  devtool: 'source-map',
  resolve: {
    /* Path Shortcuts */
    alias:{
      /* root */
      '~': path.resolve(__dirname, `${ source }/js`),
      Components: path.resolve(__dirname, `${ source }/js/components`),
      Layouts: path.resolve(__dirname, `${ source }/js/layouts`),
      Pages: path.resolve(__dirname, `${ source }/js/pages`)
    }
  }
});

mix.js(`${ source }/js/myapp.js`, `${ public }/js`);
mix.sass(`${ source }/sass/myapp.scss`, `${ public }/css`, {
  outputStyle: mix.inProduction() ? 'compact' : 'expanded'
});
mix.sourceMaps();
mix.browserSync({
  proxy: 'laratt.test',
  host: 'laratt.test',
  files: [
    `${ source }/views/**/*.php`,
    `${ public }/js/**/*.js`,
    `${ public }/css/**/*.css`
  ],
  browser: 'firefox',
  ghostMode: false,
  open: 'external'
});

mix.extract([
  'vue'
]);

mix.version();
if (mix.inProduction()) {
  mix.disableNotifications();
}


