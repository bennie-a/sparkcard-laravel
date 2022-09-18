<template>
    <button class="ui button" @click="download" :class="color">
        <slot></slot>
    </button>
</template>
<script>
import { write } from "../../composables/CSVWriter";
import BaseItemContents from "../../csv/BaseItemContents";
import Contentsfactory from "../../csv/ContentsFactory";

export default {
    props: {
        color: { type: String, default: "purple" },
        filename: { type: String, reqiured: true },
    },
    methods: {
        download: function () {
            this.$store.dispatch("setLoad", true);

            let contents = Contentsfactory.get(this.filename);
            const header = contents.header;
            const card = this.$store.getters.card;
            const jsonArray = card.map((c) => contents.contents(c));

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
