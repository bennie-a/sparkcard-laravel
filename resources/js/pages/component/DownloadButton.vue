<template>
    <button class="ui button" @click="download" :class="color">
        <slot></slot>
    </button>
</template>
<script>
import { CSV_HEADERS } from "../../cost/CsvHeader";
import { write } from "../../composables/CSVWriter";
import JsonContents from "../../repositories/JsonContents";

export default {
    props: {
        color: { type: String, default: "purple" },
        filename: { type: String, reqiured: true },
        json: { reqiured: true },
    },
    methods: {
        download: function () {
            this.$store.dispatch("setLoad", true);

            const header = CSV_HEADERS[this.filename];
            const card = this.$store.getters.card;
            let contents = JsonContents();
            const jsonArray = card.map((c) => contents.base_item(c));

            const csv = this.$papa.unparse({
                fields: header,
                data: JSON.stringify(jsonArray),
            });
            write(csv, `${this.filename}.csv`);
            this.$store.dispatch(
                "setSuccessMessage",
                "CSVのダウンロードが完了しました。"
            );

            this.$store.dispatch("setLoad", false);
        },
    },
};
</script>
