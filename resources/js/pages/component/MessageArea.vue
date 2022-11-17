<template>
    <div v-if="!$store.getters.isLoad">
        <div class="ui positive message" v-if="$store.state.msg.success != ''">
            <div class="header">
                {{ $store.state.msg.success }}
            </div>
        </div>
        <div
            class="ui negative message"
            v-if="this.$store.getters['message/error'] != ''"
        >
            <div class="header">
                {{ this.$store.getters["message/error"] }}
            </div>
        </div>
        <div
            class="ui negative message"
            v-if="this.$store.getters['message/errorhtml'] != ''"
        >
            <div class="header">
                <div v-html="$store.getters['message/errorhtml']"></div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    mounted: function () {
        this.$store.dispatch("message/clear");
    },
};
</script>
