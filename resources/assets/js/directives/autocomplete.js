
Vue.directive('autocomplete', {
    twoWay: true,
    params: ['source'],
    bind: function () {
        this.vm.$nextTick(() => {
        console.log('bind')
        var vm = this.vm;
        var key = this.expression;
        var self = this;

        var data = vm.$get(this.params.source);


            $(this.el).typeahead({
                hint: true,
                highlight: true,
                minLength: 0
            }, {
                source: function(req, callback) {
                    callback(data.filter(function(item) {
                        return item.toString().toLowerCase().indexOf(req.toLowerCase()) !== -1;
                    }));
                }
            });
        });

    },
    update: function (value) {
        if(value == undefined)
            value = '';
        $(this.el).val(value);
        $(this.el).typeahead('val', value);
    },
    unbind: function () {
        $(this.el).typeahead('destroy');
    }
});
