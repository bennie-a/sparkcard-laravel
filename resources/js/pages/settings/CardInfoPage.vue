<template>
    <message-area></message-area>
    <section class="mt-1">
        <file-upload @action="upload" type="json"></file-upload>
        {{ filename }}
    </section>
</template>
<script>
import FileUpload from "../component/FileUpload.vue";
import MessageArea from "../component/MessageArea.vue";

export default {
    data() {
        return { filename: "ファイルを選択してください" };
    },
    methods: {
        upload: function (file) {
            this.filename = file.name;
            // console.log(file.name);
        },
    },
    components: {
        "file-upload": FileUpload,
        "message-area": MessageArea,
    },
};
</script>
