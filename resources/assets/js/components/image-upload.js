App.Components.ImageUpload = Vue.extend({
    template: "#image-upload-template",
    data: function () {
        return {
            progress: 0,
            loading: 0
        }
    },
    props: {
        breeder: null,
        cloud_settings_name: {
            type: String,
            default: "upload_images"
        },
    },
    watch: {
/*
        breeder: function () {
            this.initUploader();
        }
*/
    },
    computed: {
    },
    methods: {
        // initUploader: function () {
        //     var self = this;

        //     $(this.$els['image']).unsigned_cloudinary_upload(this.cloud_settings_name,
        //         { cloud_name: App.cloud_name},
        //         { multiple: false }
        //     ).bind('fileuploadstart', function(e, data) {

        //         self.loading = 1;
        //     }).bind('cloudinarydone', function(e, data) {
        //         self.loading = 0;
        //         self.breeder.image = Object.assign({}, {
        //             name : data.result.public_id,
        //             path : data.result.url,
        //             temp : false,
        //             oldImage: self.breeder.image.oldImage,
        //             delete: self.breeder.image.delete,
        //         });
        //         self.$broadcast('image-uploaded', data.result.image);
        //     }).bind('fileuploadfail', function(e, data) {
        //         self.loading = 0;
        //     });

        // },
        // Image resize/cropper
        // =====================================
        imageCropperModal: function(input) {
            var self = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var $cropmodal = $(self.$els['imageUploadCropperModal']);
                var $cropcontainer = $cropmodal.find('.image-cropper-container');
                reader.onload = function (e) {
                    $cropcontainer.attr('src', e.target.result);
                    var cropper_margintop = $cropmodal.parents('.modal.in').scrollTop();
                    $cropmodal.find('.modal-dialog').css('margin-top', cropper_margintop+'px');
                    $cropmodal.modal('show');
                    $cropcontainer.cropper({
                        aspectRatio: 1,
                        autoCropArea: 0.3
                    });
                }
                reader.readAsDataURL(input.files[0]);
                $(document).one('hide.bs.modal', '.image-upload-cropper-modal', function(event){
                    if($(event.target).is('.image-upload-cropper-modal')){
                        $cropcontainer.cropper('destroy');
                        $(input).val('');
                    }
                });
            }
        },
        uploadImage: function() {
            var self = this;
            self.loading = 1;
            var $cropmodal = $(self.$els['imageUploadCropperModal']);
            var $cropcontainer = $cropmodal.find('.image-cropper-container');
            $cropcontainer.cropper('getCroppedCanvas').toBlob(function(blob) {
                $cropmodal.modal('hide');
                var formData = new FormData();
                formData.append('file', blob);
                formData.append('upload_preset', App.cloud_preset);
                var cloud_upload_url = App.cloud_api_base_url + '/image/upload';
                $.ajax({
                    url: cloud_upload_url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function(res) {
                    self.breeder.image = Object.assign({}, {
                        name : res.public_id,
                        path : res.url,
                        temp : false,
                        oldImage: self.breeder.image.oldImage,
                        delete: self.breeder.image.delete,
                    });
                    self.$broadcast('image-uploaded', res.image);
                }).fail(function(err) {
                    console.log('Upload error');
                    console.log(err);
                }).complete(function() {
                    self.loading = 0;
                    self.initUploader();
                });
            });
        },

        initUploader: function () {
            var self = this;
            $(this.$els['image']).on('change', function(event) {
                console.log('input change');
                self.imageCropperModal(this);
            })
        },
        // =====================================
        uploaderHelper: function () {
            $(this.$els['image']).click();
        },
        deleteImage: function () {
            this.breeder.image = {name: "", path: "", temp: true, oldImage: "", delete: false};
            $(this.$els['image']).change();
        },
        resetFileField: function (e) {
            e.wrap('<form>').closest('form').get(0).reset();
            e.unwrap();
        },
        closeCropperModal: function(e) {
            $('.image-upload-cropper-modal').modal('hide');
        },
    },
    ready: function () {
        this.initUploader();
    }

});
