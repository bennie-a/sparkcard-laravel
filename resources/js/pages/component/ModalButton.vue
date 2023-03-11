<template>
    <button class="ui teal button" @click="show"><slot></slot></button>
    <div id="confirm" class="ui tiny modal">
        <div class="header">Notice</div>
        <div class="content" v-if="this.$store.getters.isLoad == false">
            登録してもよろしいですか?
        </div>
        <div class="actions" v-if="this.$store.getters.isLoad == false">
            <button class="ui cancel button">
                <i class="close icon"></i>キャンセル
            </button>
            <button class="ui teal button" @click="execute">
                <i class="checkmark icon"></i>登録する
            </button>
        </div>
        <now-loading></now-loading>
    </div>
</template>
<script>
import NowLoading from "../component/NowLoading.vue";

export default {
    emits: ["action"],
    methods: {
        show: function () {
            $("#confirm").modal("show");
        },
        execute: function () {
            this.$store.dispatch("setLoad", true);
            this.$emit("action");
            $("#confirm").modal("hide");
            this.$store.dispatch("setLoad", false);
        },
    },
    components: {
        "now-loading": NowLoading,
    },
};
</script>
