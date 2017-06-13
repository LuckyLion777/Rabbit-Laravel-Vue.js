import Clipboard from 'clipboard'

new Clipboard('.btn');

(function (viewport) {

    App.settings = {
        debug: false,
        tablet: -1 != navigator.userAgent.indexOf("iPad"),
        desktop: null === navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i),
        mobile: -1 != navigator.userAgent.indexOf("iPhone"),
        android: -1 != navigator.userAgent.toLowerCase().indexOf("android"),
        timer: null
    };

    App.allmobiles = function() {
        //return true;
        var check = false;
        (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
        return check;
    };


    $(function () {
        App.vent.on('export-schedule', function () {
            $('#export-schedule-modal').modal('show');
            $('#export-schedule-url').focus().select();
        });
        App.vent.on('export-schedule-download', function () {
            $('#export-schedule-modal').modal('hide');
            // window.location = $(this).attr('href');
        });

        $(document).on('keypress', '.js_only-numbers', function (eve) {
            if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0)) {
                eve.preventDefault();
            }
        });

        $(document).on('keyup', '.js_only-numbers', function (eve) {
            if ($(this).val().indexOf('.') == 0) {
                $(this).val($(this).val().substring(1));
            }
        });


        $(document).keydown(function (e) {
            if (e.keyCode == 27) {
                $('.modal').modal('hide');
            }
        });

        $('.box-tools').on('click', function (e) {
            e.preventDefault();
            return false;
        });

        $('#sidebar-new-breeder').on('click', function () {
            App.vent.trigger('breeders.new');
            return false;
        });


        $('#sidebar-new-litter').on('click', function () {
            App.vent.trigger('litters.new');
            return false;
        });

        $('#sidebar-new-ledger').on('click', function (e) {
            e.preventDefault();
            App.vent.trigger('ledger.new');
        });

        $(window).on('popstate', function() {
            $('.modal').modal('hide');
        });

        $(document)                            //Issue #89
            .on('shown.bs.modal', function() {
                const $body = $('body');
                $body.css({
                    position: 'fixed', width: '100%',
                    'margin-top': -1 * ($body.position().top || $body.scrollTop())  + 'px'
                });
            })
            .on('hidden.bs.modal', function() {
                const $body = $('body'), top = -1 * parseFloat($body.css('margin-top'));
                $body.css({ position: '', width: '', 'margin-top': 0 });
                $('html,body').scrollTop(top);
            });

        if(viewport.is("xs")) {
            App.viewportXS = true;
        }

        $('aside.main-sidebar').find('li').not('.treeview').find('a').on('click', function () {
            if(App.viewportXS){
                $('.sidebar-toggle').trigger('click');
            }
        });

        $(window).resize(
            viewport.changed(function() {
                if(viewport.is('xs')) {
                    App.viewportXS = true;
                } else {
                    App.viewportXS = false;
                }
            })
        );

        App.parseMoment = function (date) {
            return typeof date === 'string' && date.indexOf("-") === -1
                    ? moment(date, App.dateFormat)
                    : moment(date);
        };

        App.formatDate = function(date){
            return App.parseMoment(date).format(App.dateFormat);
        };

        /**
         * Rounds number to toDigits number is total (or to integral value, if it already has >= toDigits digits).
         *
         * Examples:
         * App.roundTo(10.43526, 2) = 10
         * App.roundTo(6.354534, 2) = 6.4
         * App.roundTo(0.4366, 2)   = 0.4
         *
         * @param n
         * @param toDigits
         */
        App.roundTo = (n, toDigits) => {
            const int = Math.ceil(n);
            let floatDigits = toDigits - int.toString().length;
            if (floatDigits < 0) {
                floatDigits = 0;
            }
            const mult = Math.pow(10, floatDigits);

            return Math.round(n * mult) / mult;
        };

        App.vent.openBirthModal = function(breed_id) {
            const router = new VueRouter();

            App.dashboard_birth_breed = breed_id;
            router.go({path: '/'});
            App.vent.trigger('dashboard-birth-breed', [breed_id]);
            return false;
        };

        App.vent.butcherModal = function(litter_id) {
            const router = new VueRouter();

            App.dashboard_butch_litter = litter_id;
            router.go({path: '/'});
            App.vent.trigger('dashboard-butch-litter', [litter_id]);
            return false;
        };

        App.vent.weightModal = function(litter_id) {
            const router = new VueRouter();

            App.dashboard_weight_litter = litter_id;
            router.go({path: '/'});
            App.vent.trigger('dashboard-weight-litter', [litter_id]);
            return false;
        };

        function hexToRgb(hex) {
            var bigint = parseInt(hex, 16);
            var r = (bigint >> 16) & 255;
            var g = (bigint >> 8) & 255;
            var b = bigint & 255;

            return [r, g, b].join();
        }

        var colours = [
            'dd4b39',
            '00a65a',
            'f39c12',
            '00c0ef',
            '3c8dbc',
            '39cccc',
            '605ca8',
            'd81b60',
            'ff851b',
            '111111'
        ];

        Chart.defaults.global.colours = [];
        _.each(colours, function(color){
            var rgb =  hexToRgb(color);
            Chart.defaults.global.colours.push(
                {
                    backgroundColor : "rgba(" + rgb + ",0.2)",
                    borderColor: "rgba(" + rgb + ",1)",
                    borderWidth: 1,
                    hoverBackgroundColor: "rgba(" + rgb + ",0.4)",
                    hoverBorderColor: "rgba(" + rgb + ",1)"
                }
            )
        });
    });

})(ResponsiveBootstrapToolkit);
