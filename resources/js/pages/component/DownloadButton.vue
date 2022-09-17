<template>
    <button class="ui button" @click="download" :class="color">
        <slot></slot>
    </button>
</template>
<script>
export default {
    props: ["color"],
    methods: {
        download: function () {},
    },
};
</script>
