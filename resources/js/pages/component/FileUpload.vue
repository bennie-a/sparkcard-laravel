<template>
    <input
        id="embedpollfileinput"
        type="file"
        class="inputfile"
        @change="onFileChange"
    />
    <label for="embedpollfileinput" class="ui teal basic button uploadbutton">
        <i class="file icon"></i>
        ファイルを選択する
    </label>
</template>
<style scoped>
.inputfile {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
}
</style>
<script>
export default {
    emits: ["action"],
    props: {
        type: { type: String, default: "csv" },
    },
    methods: {
        // ファイルアップロードイベント
        onFileChange: function (e) {
            this.$store.dispatch("message/clear");

            const file = e.target.files[0];
            if (file == undefined) {
                return;
            }
            let fileType = file.name.split(".").pop();
            if (fileType !== this.type) {
                this.$store.dispatch(
                    "message/error",
                    `ファイルは${this.type}ファイルを選択してください。`
                );
                return;
            }
            this.$emit("action", file);
            this.$store.dispatch("setLoad", false);
        },
    },
};
</script>
