App.Components.Datepicker = {
    template: "#datepicker-template",
    data: function () {
        return {
            disable_past: false
        }
    },
    props: ['model', 'disable_past'],
    watch: {
        model: function (value) {
            $(this.el).datepicker('setDate', value);
        }
    },
    methods: {

    },
    ready: function () {
        console.log(this.model);
        $(this.$els.input).val(this.model);
        $(this.el).datepicker({
            format: App.dateFormat.toLowerCase(),
            todayBtn: "linked",
            autoclose: true,
            clearBtn: true,
            todayHighlight: true,
            disableTouchKeyboard: true,
            toggleActive: true,
            defaultDate: new Date(),
            startDate: this.disable_past ? new Date() : 0
        }).on('show', function (e) {
            hasActivated = true;
            $(this.el).datepicker('setDate', this.value);
            /**
             * hack to disable touch keyboard with blur() method,
             * but only if the datepicker is not visible (to minimize accessibility problems)
             * so with a second click, the keyboard appears
             */
            //if ((window.navigator.msMaxTouchPoints || 'ontouchstart' in document)) {
            //    if (!isDatepickerVisible) {
            //        $('input', $(this))[0].blur();
            //    }
            //}
            //isDatepickerVisible = true;

        }).on('hide', function (e) {
            hasActivated = false;
            //isDatepickerVisible = false;
        }).on('changeDate', function (e) {
            console.log('changeDate');
            //if (this.hasActivated) {
            //    self.vm.$set(key, e.format());
            //}
        }).on('change', function () {
            console.log('change', $(this).val());
        });
    }
}
