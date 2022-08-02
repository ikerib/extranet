var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('js/app', ['./assets/js/fontawesome-5.0.13.all.js','./assets/js/app.js'])
    .addEntry('js/dropzone', ['./assets/js/dropzone.js'])


    .addStyleEntry('css/app', ['./assets/css/app.scss', './assets/css/dashboard.scss'])
    .addStyleEntry('css/login', './assets/css/login.scss')
    .addStyleEntry('css/dropzone', './assets/css/dropzone.css')
    // .addStyleEntry('css/dashboard', './assets/css/dashboard.scss')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

;

module.exports = Encore.getWebpackConfig();

