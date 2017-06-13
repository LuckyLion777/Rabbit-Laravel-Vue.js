;(function () {

    App.MobileTypes = function () {

        if(!App.allmobiles()) {
            return false;
        }

        $('[data-mobile-type]').each(function (i, el) {
            var $this = $(el);
            var type = $this.data('mobile-type');

            $this.attr('type', type);

            if(type == "date") {
                $this.focusout(function () {
                    $this.attr('type', 'text');
                    $this.val(App.formatDate($this.val()));
                }).on('touchstart', function () {
                    if($this.attr('type') == "text") {
                        $this.attr('type', type);
                    }
                });
            }

        });

    };

})();
