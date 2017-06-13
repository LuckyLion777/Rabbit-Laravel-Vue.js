<template id="broadcast-alert-template">
    <div id="broadcast-alert" class="alert alert-warning alert-dismissible" v-show="existing && show">
        <button class="close" type="button" v-on:click="dismiss" aria-hidden="true">&times;</button>
        <h4>@{{ title }}</h4>
        <p>@{{{ content }}}</p>
    </div>
</template>
