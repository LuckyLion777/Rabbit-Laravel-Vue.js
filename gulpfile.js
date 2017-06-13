var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */
elixir.extend('sourcemaps', false);

elixir.config.css.autoprefix = {
    enabled: true, //default, this is only here so you know how to disable
    options: {
        cascade: true,
        browsers: ['last 2 versions', '> 0.5%']
    }
};

elixir(function (mix) {

    mix.less([
        'bootstrap-less/bootstrap.less',
        'AdminLTE/AdminLTE.less',
        'AdminLTE/skins/_all-skins.less',
        'fontawesome/font-awesome.less',
        'custom.less'
    ], 'public/css/libs.css');

    //mix.less('custom.less', 'public/css/custom.css');


    /*  PLUGINS  */

// plugins path
    var plugins = "../plugins/";

// plugins scripts
    mix.scripts([
        plugins + 'jQuery/jQuery-2.2.0.min.js',
        plugins + 'jQueryUI/jquery-ui.min.js',
        plugins + 'responsive-bootstrap-toolkit/bootstrap-toolkit.min.js',
        plugins + 'daterangepicker/moment.js',
        plugins + 'fastclick/fastclick.js',
        plugins + 'datepicker/bootstrap-datepicker.js',
        plugins + 'daterangepicker/daterangepicker.js',
        plugins + 'bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js',
        plugins + 'knob/jquery.knob.js',
        plugins + 'iCheck/icheck.js',
        plugins + 'typeahead/typeahead.bundle.js',
        plugins + 'typeahead/typeahead.jquery.js',
        plugins + 'chartjs/Chart.js',
        plugins + 'cropper/cropper.min.js',
        plugins + 'JavaScript-Canvas-to-Blob-master/canvas-to-blob.min.js',
        plugins + 'intro/intro.min.js'
    ], 'public/js/plugins.js');

// plugins css
    mix.styles([
        plugins + 'datepicker/datepicker3.css',
        plugins + 'daterangepicker/daterangepicker-bs3.css',
        plugins + 'bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
        plugins + 'iCheck/square/_all.css',
        plugins + 'bootstrap-horizon/bootstrap-horizon.css',
        plugins + 'typeahead/typeahead.css',
        plugins + 'intro/introjs.min.css',
        plugins + 'cropper/cropper.min.css',
    ], 'public/css/plugins.css');

// plugins css
//    mix.styles([
//        '../../../AdminLTE-master/custom.css'  // client styles!!!
//    ], 'public/css/custom.css');


    /*  CUSTOM JS  */

    mix.scripts([
        'libs/registerElement.js',
        'libs/vue.js',
        'libs/vue-router.js',
        'libs/vue-resource.js',
        'libs/vue-validator.js',
        'libs/vue-element.js',
        'libs/underscore.js',
        'libs/jquery.iframe-transport.js',
        'libs/jquery.fileupload.js',
        'libs/jquery.cloudinary.js',
        'libs/bootstrap.min.js',
        'libs/admin-app.js',
        'libs/sha1.js',
        'demo.js'
        //'pages/dashboard.js'
    ], 'public/js/libs.js');

    mix.browserify([
        'config.js',
        'api/api.js',
        'service/webpush.js',
        'service/service.js',
        'mixins/butcherable.js',
        'mixins/deathReason.js',
        'mixins/currency.js',
        'mixins/filterable.js',
        'mixins/subscribes.js',
        'mixins/breedersFilter.js',
        'components/exploitable/form.js',
        'components/exploitable/section.js',
        'components/sex-select.js',
        'components/lbsoz-input.js',
        'components/image-upload.js',
        'components/reports/line-chart.js',
        'components/settings-image-upload.js',
        'directives/datepicker.js',
        'directives/daterangepicker.js',
        'directives/autocomplete.js',
        'components/broadcast-form.js',
        'components/broadcast-alert.js',
        'directives/infinite-scroll.js',
        'components/task-form.js',
        'components/birth-form.js',
        'components/breeder-form.js',
        'components/breeder-butcher.js',
        'components/pedigree-form.js',
        'components/copy-pedigree-form.js',
        'components/litter-form.js',
        'components/litter-box.js',
        'components/litter-butcher.js',
        'components/litter-weight.js',
        'components/kit-form.js',
        'components/import-ledgers-file.js',
        'components/userList.js',
        'components/user.js',
        'components/schedule-calendar.js',
        'components/upcomming-tasks.js',
        'components/schedule.js',
        'components/dashboard.js',
        'components/reports/reports.js',
        'components/breeders.js',
        'components/breeder_category-form.js',
        'components/breeder_categories.js',
        'components/litters.js',
        'components/ledger-form.js',
        'components/ledger-table.js',
        'components/cage-cards/print.js',
        'components/litter-profile.js',
        'components/pedigrees.js',
        'components/profile.js',
        'components/settings.js',
        'components/wizard/wizard.js',
        'components/wizard/wizard__settings.js',
        'components/wizard/wizard__breeders.js',
        'components/account-settings.js',
        'components/notification-tab.js',
        'components/tour-link.js',
        'components/kit-pedigree.js',
        'components/ledger.js',
        'components/ledger_category-form.js',
        'components/ledger_categories.js',
        'components/wizard/wizard__ledgers.js',
        'custom.js',
        'mobiletypes.js',
        'components/cage-cards/template-form.js',
        'components/cage-cards/templates-list.js',
        'components/qrcode/qrcode.js',
        'vue-app.js'
    ], 'public/js/vue-app.js');

    mix.browserify(['service/worker.js'], 'public/service-worker.js');
});
