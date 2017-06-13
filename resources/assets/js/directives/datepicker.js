
Vue.directive('datepicker', {
    twoWay: true,
    params: ['container'],
    bind: function () {
        var vm = this.vm;
        var key = this.expression;
        var self = this;
        var disable_past = $(this.el).find('input').data('disable-past');
        var hasActivated = false;
        this.editing = false;
        $(this.el).datepicker({
            format: App.dateFormat.toLowerCase(),
            todayBtn: "linked",
            autoclose: true,
            clearBtn: true,
            todayHighlight: true,
            disableTouchKeyboard: true,
            toggleActive: true,
            defaultDate: new Date(),
            startDate: disable_past ? new Date() : 0,
            forceParse: true,
            orientation: "auto",
            container: self.params && self.params.container? self.params.container : 'body'
        }).on('changeDate', function(e){
            if($(self.el).find('input').val().length > 7) {
                self.vm.$set(key, e.format());
            }
        }).on('clearDate', function(e){
            self.vm.$set(key, null);
        });

        $(this.el).find('input').on('change paste keyup', function(){
            if(this.value == ''){
                self.vm.$set(key, '');
            }
            self.value = $(this.el).find('input').val();
        }).on('click', function(){
            $(this).select();
        });
    },
    update: function (value) {
        $(this.el).find('input').val(value);
        if(!value || value == ''){
            value = '';
            $(this.el).datepicker('update', '');
        } else {
            $(this.el).datepicker('setDate', value);
        }
        $(this.el).val(value);
    },
    unbind: function () {
        $(this.el).datepicker('destroy');
    }
});
