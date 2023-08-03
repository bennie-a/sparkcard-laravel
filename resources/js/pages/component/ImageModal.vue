<template>
    <div class="ui tiny modal" v-bind:id="this.id">
        <i class="close icon"></i>
        <div class="image content">
            <img v-bind:src="this.url" class="image" />
        </div>
    </div>
</template>
<script>
export default {
    name: "image-modal",
    props: {
        url: { type: String, required: true },
        id: { type: Number, required: true },
    },
    methods: {
        showImage: function (id) {
            const selecterId = `#${id}`;
            $(selecterId).modal("show");
        },
    },
};
</script>
<style scoped>
div.card > .image {
    height: 120px !important;
    overflow: hidden;
    /* height: min-content; */
}
div.image img {
    width: fit-content;
    height: 100% !important;
    object-position: 50% 20%;
    object-fit: cover;
    cursor: pointer;
}
</style>
