<template>
    <message-area></message-area>
    <section class="mt-1">
        <file-upload @action="upload" type="json"></file-upload>
        {{ filename }}
    </section>
    <section>{{ item }}</section>
</template>
<script>
import FileUpload from "../component/FileUpload.vue";
import MessageArea from "../component/MessageArea.vue";
import axios from "axios";
export default {
    data() {
        return { filename: "ファイルを選択してください", item: [] };
    },
    methods: {
        upload: async function (file) {
            this.filename = file.name;
            const config = {
                headers: {
                    "Content-Type": "application/json",
                },
            };
            await axios
                .post("/api/upload/card", file, config)
                .then((response) => {
                    if (response.status == 201) {
                        this.item = response.data;
                    }
                })
                .catch((e) => {
                    if (e.response.status == 422) {
                        const errors = e.response.data.errors;
                        let msgs = "<ul>";
                        for (let key in errors) {
                            console.log(key);
                            let array = errors[key];
                            array.forEach((msg) => {
                                msgs += `<li>${msg}</li>`;
                            });
                        }
                        msgs += "</ul>";
                        // $keys = Object.keys(errors);
                        // console.log($keys);
                        // errors.forEach(function (e, key) {
                        // });
                        this.$store.dispatch("message/errorhtml", msgs);
                    }
                });
        },
    },
    components: {
        "file-upload": FileUpload,
        "message-area": MessageArea,
    },
};
</script>
