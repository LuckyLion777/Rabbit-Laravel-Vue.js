/**
 * Sadly enough, directives binding seems to happen before nodes are inserted into DOM, whereas
 * authors of daterangepicker use some dom api upon initilizing. So the only way to make this all work seems to be:
 * * first, bind event when binding directive
 * * then, on each usage, trigger document event when elements are inserted
 */
Vue.directive('daterangepicker', {
    twoWay: true,
    bind: function () {
        if (!this.el.id) {
            console.error('daterangepicker directive cannot work on element without id, sorry!');
        }
        //console.info('daterangepicker detected id: ' + this.el.id);
        this.reset = false;
        this.getFrom = function(value) {
            if (!value || !value.length){
                this.reset = true;
                return null;
            }
            return value[0];
        };
        this.getTo = function(value) {
            if (!value || !value.length) return null;
            return value[1];
        };
        this.hasActivated = false;

        $(document).on('triggerDaterangepicker', '#' + this.el.id, function(e) {
            if (this.hasActivated) return;
            this.hasActivated = true;

            //console.log('triggered', $(this.el), $.contains(document, this.el) ? 'CONTAINS' : 'NOT CONTAINS');

            $(this.el).daterangepicker({
                timePicker: false,
                format: App.dateFormat,
                locale: {
                    cancelLabel: 'Clear'
                },
                autoUpdateInput: true,
                opens: 'center'
            }, function(start, end, label) {
                // console.log(start, end);
                this.vm.$set(this.expression, [start, end]);
            }.bind(this));

            $(this.el).on('cancel.daterangepicker', function(ev, picker) {
                $(this.el).val('');
                this.vm.$set(this.expression, []);
            }.bind(this)).on('apply.daterangepicker', function(ev, picker) {
                this.vm.$set(this.expression, [picker.startDate, picker.endDate]);
            }.bind(this));

            if (this.value) {
                $(this.el).data('daterangepicker').setStartDate(this.getFrom(this.value));
                $(this.el).data('daterangepicker').setEndDate(this.getTo(this.value));
            }
        }.bind(this));

       // console.log('bound', $(this.el));
    },
    update: function (value) {
        var from = this.getFrom(value);
        var to   = this.getTo(value);

        if (this.hasActivated) {
            if(this.reset){
                $(this.el).data('daterangepicker').setStartDate(moment());
                $(this.el).data('daterangepicker').setEndDate(moment());
                $(this.el).val('');
            } else {
                $(this.el).data('daterangepicker').setStartDate(from);
                $(this.el).data('daterangepicker').setEndDate(to);
            }
        }
        this.reset = false;
    },
    unbind: function () {
        // if (this.hasActivated) {
        //     $(this.el).data('daterangepicker').destroy();
        // }
        $(document).off('triggerDaterangepicker', '#' + this.el.id);
        //console.log('unbound', $(this.el));
    }
});
