<template>
    <div v-show="isLoad">
        <div class="loader"></div>
        <p class="text-center ui large header">waiting...</p>
    </div>
</template>
<script>
export default {
    computed: {
        isLoad: function () {
            return this.$store.state.isLoad;
        },
    },
};
</script>
