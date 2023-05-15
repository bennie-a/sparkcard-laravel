<template>
    <input
        type="file"
        class="inputfile"
        id="embedpollfileinput"
        @change="onFileChange"
    />
    <label for="embedpollfileinput" class="ui teal button uploadbutton">
        <i class="bi bi-cloud-upload-fill mr-half"></i>
        <slot></slot>
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

label.uploadbutton {
    margin: 0 auto;
}
</style>

<script>
export default {
    emits: ["upload", "download"],
    methods: {
        toggle: function () {
            $("#upload").modal("show");
        },
        onFileChange: function (e) {
            this.$store.dispatch("message/clear");
            const file = e.target.files[0];
            if (file == undefined) {
                return;
            }
            let fileType = file.name.split(".").pop();
            if (fileType !== "csv") {
                this.$store.dispatch(
                    "message/error",
                    "商品ファイルはCSVファイルを選択してください。"
                );
            }

            this.$emit("upload", file);
        },
        download: function () {
            this.$emit("download");
            this.downloadCancel();
            this.$store.dispatch(
                "setSuccessMessage",
                "CSVのダウンロードが完了しました。"
            );
        },
        downloadCancel: function () {
            $("#download").modal("hide");
        },
    },
};
</script>
