App.Components.User = {
    template: "#user-template",
    data: function () {
        return {
            userRoles: [],
            errors: [],
            editMode: false,
            user: {
                trial_ends: "",
                "image": {
                    "name": '',
                    "path": '',
                    "temp": true,
                    "oldImage": '',
                    "delete": false,
                }
            }
        }
    },
    events: {
        dataHere: function () {
            this.$nextTick(function () {
                this.initUploader();
            });
        }
    },
    methods: {
        deleteImage: function () {
            this.user.image.path = '';
            this.user.image.delete = true;
        },
        sendUser: function () {
            this.errors = [];
            if (this.editMode)
                this.user._method = "PUT";
            //this.user.image = this.images;
            api.saveUser(this.user).then(
                () => { this.$route.router.go({path: '/users'}); },
                response => {
                    if (response.data) {
                        this.errors = response.data;
                    }
                }
            );
        },
        initUploader: function () {
            var self = this;
            $(self.$els['image']).fileupload({
                dataType: 'json',
                paramName: 'image',
                formData: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "_method": "POST",
                    "user_id": self.user.id
                },
                url: '/admin/images/uploadImage',
                done: function (e, data) {
                    self.user.image.name = data.result.image.name;
                    self.user.image.path = data.result.image.path;
                    self.user.image.temp = data.result.image.temp;
                }
            });
        }
    },
    ready: function () {
        api.getRolesList().then(rolesList => {
            this.userRoles = rolesList;
        });
        if (this.$route.params.userId) {
            this.editMode = true;
            api.getUser(this.$route.params.userId).then(user => {
                this.user = user;
            });
        }
        this.initUploader();
    }
};
