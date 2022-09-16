<template>
    <button class="ui pink button" @click="toggle">
        <slot></slot>
    </button>
    <div id="upload" class="ui mini modal">
        <i class="close icon"></i>
        <div class="header">ファイルをアップロード</div>
        <div class="content">
            <input
                type="file"
                class="inputfile"
                id="embedpollfileinput"
                @change="onFileChange"
            />
            <label
                for="embedpollfileinput"
                class="ui big pink button uploadbutton"
            >
                <i class="ui upload icon"></i>
                商品ファイル読み込み
            </label>
        </div>
    </div>
    <div id="download" class="ui mini modal">
        <i class="close icon"></i>
        <div class="header">在庫ファイルをダウンロードしますか?</div>
        <div class="content text-center">
            <button class="ui primary button" @click="">OK</button>
            <button class="ui button" @click="downloadCancel">
                キャンセル
            </button>
        </div>
    </div>
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
    display: block;
    margin: 0 auto;
}
</style>

<script>
export default {
    emits: ["upload"],
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
            $("#upload").modal("hide");
            $("#download").modal("show");
        },
        downloadCancel: function () {
            $("#download").modal("hide");
        },
    },
};
</script>
