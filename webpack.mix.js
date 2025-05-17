const mix = require('laravel-mix');


mix.js('resources/js/app.js', 'assets/js/admin.js')
   .vue({ version: 3 }) // Ensure Vue 3 compatibility
    .js('resources/js/listing-app/listing.js', 'assets/js/listing-app.js')
    .js('resources/js/listing-app/listing-details.js', 'assets/js/listing-details.js')
   .sass('resources/scss/admin/app.scss', 'assets/css/admin.css')
   .sass('resources/scss/public/listing-details.scss', 'assets/css/listing-details.css')
   .copyDirectory('resources/images', 'assets/images')
   .sourceMaps();

// Explicitly configure Webpack resolve.extensions
mix.webpackConfig({
    resolve: {
        extensions: ['.*']
    }
});