<template id="image-upload-template">
    <div class="form-group col-sm-12">
        <label class="col-sm-2 col-xs-3 control-label">Photo</label>
        <div class="col-sm-3 col-xs-5">

            <div v-if="breeder.image.name">
                <div v-if="!breeder.image.delete">
                    <a href="#" @click.prevent="deleteImage" class="pull-right image-remove-icon"><i
                                class="fa fa-times"></i></a>
                </div>
                <img class="img-responsive img-circle profile-user-img" v-bind:alt="breeder.name"
                     v-bind:src="breeder.image.path" v-if="breeder.image.path">
            </div>
            <img @click="uploaderHelper" class="img-responsive img-circle profile-user-img" src=
            "{{ asset('media/breeders/default.jpg') }}" v-if="!breeder.image.name">

        </div>
        <div class="col-sm-7 pull-right">
            <input v-el:image type="file" name="file" v-bind:alt="breeder.name">
            <img v-if="loading" src="/img/ajax-loader.gif" alt="Loading..." class="loader">
            {{--<p class="help-block">Select photo of litter</p>--}}
        </div>
    </div>
    <!-- image cropper modal -->
    <div v-el:image-upload-cropper-modal class="modal image-upload-cropper-modal image-cropper-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success bg-info">
                    <button type="button" class="close" aria-label="Close" @click.prevent="closeCropperModal()"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Resize image</h4>
                </div>
                <div class="modal-body">
                    <img class="image-cropper-container" />
                </div>
                <div class="modal-footer bg-success bg-info">
                    <button type="button" class="btn btn-default pull-left" @click.prevent="closeCropperModal()">Close</button>
                    <button type="button" class="btn btn-success btn-info" @click.prevent="uploadImage()">Crop/Upload</button>
                </div>
            </div>
        </div>
    </div><!-- /.image cropper modal -->
</template>

<template id="image-upload-row-template">
    <div class="form-group">
        <div class="col-sm-3">

            <div v-if="breeder.image.name">
                <div v-if="!breeder.image.delete">
                    <a href="#" @click.prevent="deleteImage" class="pull-right image-remove-icon"><i
                                class="fa fa-times"></i></a>
                </div>
                <img class="img-responsive img-circle profile-user-img" v-bind:alt="breeder.name"
                     v-bind:src="breeder.image.path" v-if="breeder.image.path">
            </div>
            <img @click="uploaderHelper" class="img-responsive img-circle profile-user-img" src=
            "{{ asset('media/breeders/default.jpg') }}" v-if="!breeder.image.name">

        </div>
        <div class="col-sm-7">
            <input v-el:image type="file" name="file" v-bind:alt="breeder.name">
            <img v-if="loading" src="/img/ajax-loader.gif" alt="Loading..." class="loader">
            {{--<p class="help-block">Select photo of litter</p>--}}
        </div>
    </div>
</template>