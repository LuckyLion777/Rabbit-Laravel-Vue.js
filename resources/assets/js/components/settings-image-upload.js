App.Components.SettingsImageUpload = App.Components.ImageUpload.extend({
    template: '#settings-image-upload-template',
    data: function () {
        return {
            progress: 0,
            loading: 0
        }
    },
    props: ['image'],
    watch: {

    },
    computed: {
    },
    methods: {
        initUploader: function () {
            var self = this;

            $(this.$els['image']).unsigned_cloudinary_upload("upload_logos",
                { cloud_name: App.cloud_name},
                { multiple: false }
            ).bind('fileuploadstart', function(e, data) {

                self.loading = 1;
            }).bind('cloudinarydone', function(e, data) {
                self.loading = 0;
                self.image.name = data.result.public_id;
                self.image.path = data.result.url;
                self.image.temp = true;
                self.$broadcast('image-uploaded', data.result.image);
            }).bind('fileuploadfail', function(e, data) {
                self.loading = 0;
            });

        },
        uploaderHelper: function () {
            $(this.$els['image']).click();
        },
        deleteImage: function () {
            this.image = {name: "", path: "", temp: true, oldImage: "", delete: false};
            $(this.$els['image']).change();
        }
    },
    ready: function () {
        this.initUploader();
    }
});
