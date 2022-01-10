var Encore = require('@symfony/webpack-encore');

Encore

    .copyFiles([
        {from: './node_modules/ckeditor/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
        {from: './node_modules/ckeditor/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
        {from: './node_modules/ckeditor/lang', to: 'ckeditor/lang/[path][name].[ext]'},
        {from: './node_modules/ckeditor/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
        {from: './node_modules/ckeditor/skins', to: 'ckeditor/skins/[path][name].[ext]'}
    ])
// Uncomment the following line if you are using Webpack Encore <= 0.24
// .addLoader({test: /\.json$/i, include: [require('path').resolve(__dirname, 'node_modules/ckeditor')], loader: 'raw-loader', type: 'javascript/auto'})

// directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.scss) if you JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('css', './assets/css/app.scss')
    .addEntry('auction', './assets/js/auction.js')
    .addEntry('sign_auction_participant', './assets/js/sign_auction_participant.js')
    .addEntry('sign_xml_bid', './assets/js/sign_xml_bid.js')
    .addEntry('sign_organizer_xml_bid', './assets/js/organizer/sign_organizer_xml_bid.js')
    .addEntry('cryptopro', './assets/js/crypto-pro-app.js')
    .addEntry('profile_cert_list_modal', './assets/js/profile/cert-list-modal.js')
    .addEntry('sign_profile_file', './assets/js/sign_profile_file.js')
    .addEntry('sign_profile_xml', './assets/js/sign_profile_xml.js')
    .addEntry('sign_procedure_file', './assets/js/sign_procedure_file.js')
    .addEntry('sign_procedure_xml', './assets/js/sign_procedure_xml.js')
    .addEntry('sign_lot_file', './assets/js/sign_lot_file.js')
    .addEntry('bootstrap', './assets/js/bootstrap.min.js')
    .addEntry('document_show_details', './assets/js/document_show_details.js')
    .addEntry('withdraw-sign', './assets/js/profile/payments/withdraw.js')
    .addEntry('profile_cert_list_modal_css', './assets/css/profile/create.css')
    .addEntry('spinkit', './assets/css/spinkit.min.css')
    .addEntry('protocol_sign','./assets/js/protocol/sign.js')
    .addEntry('notification','./assets/js/notification.js')
    .addEntry('create_profile','./assets/js/create_profile.js')
    .addEntry('create_procedure','./assets/js/create_procedure.js')
    .addEntry('input_masks','./assets/js/input_masks.js')
    .addEntry('profile_recall_sign','./assets/js/profile/recall/sign.js')
    .addEntry('admin_main','./assets/js/admin/main.js')
    .addEntry('sign_login_form', './assets/js/sign_login_form.js')
    //.addEntry('page2', './assets/js/page2.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabel((babelConfig) => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // enables Sass/SCSS support
    .enableSassLoader()

// uncomment if you use TypeScript
    .enableTypeScriptLoader()

// uncomment to get integrity="..." attributes on your script & link tags
// requires WebpackEncoreBundle 1.4 or higher
//.enableIntegrityHashes()

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()

// uncomment if you use API Platform Admin (composer req api-admin)
//.enableReactPreset()
//.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
