<template id="settings-image-upload-template">
    <div class="col-lg-12">
        <label for="">Logo</label>
        <div v-if="image.name">
                <div v-if="!image.delete">
                    <a href="#" @click.prevent="deleteImage" class="pull-right image-remove-icon"><i
                                class="fa fa-times"></i></a>
                </div>
                <img @click="uploaderHelper" class="thumbnail" style="max-width: 300px" v-bind:alt="image.name"
                     v-bind:src="image.path" v-if="image.path">
            </div>
        </div>
    <div class="col-lg-12">
        <input v-el:image type="file" name="file" v-bind:alt="image.name">
        <img v-if="loading" src="/img/ajax-loader.gif" alt="Loading..." class="loader">
    </div>

</template>