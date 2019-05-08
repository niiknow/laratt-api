const path   = require('path');
const mix    = require('laravel-mix');
const source = 'resources';
const public = 'public';

mix.setPublicPath(path.normalize(public));

mix.webpackConfig({
  externals: {
    'jquery': 'jQuery',
    'vue': 'Vue'
  },
  devServer: { overlay: true },
  devtool: 'source-map',
  resolve: {
    /* Path Shortcuts */
    alias:{
      /* root */
      '~': path.resolve(__dirname, `${ source }/js`),
      Components: path.resolve(__dirname, `${ source }/js/components`),
      Layouts: path.resolve(__dirname, `${ source }/js/layouts`)
    }
  }
});

mix.js(`${ source }/js/app.js`, `${ public }/js`).extract();
mix.sass(`${ source }/sass/app.scss`, `${ public }/css`, {
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

mix.version().disableNotifications();
