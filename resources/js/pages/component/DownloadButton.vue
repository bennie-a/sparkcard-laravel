<template>
    <button
        class="ui button"
        @click="download"
        :class="[color, { disabled: isDisabled }]"
    >
        <slot></slot>
    </button>
    <div id="complate" class="ui mini modal">
        <div class="header">ダウンロードが完了しました。</div>
        <button id="ok" class="ui teal fluid button" @click="ok">OK</button>
    </div>
</template>
<script>
import { write } from "../../composables/CSVWriter";
import Contentsfactory from "../../csv/ContentsFactory";

export default {
    props: {
        color: { type: String, default: "teal" },
        filename: { type: String, reqiured: true },
        startnum:{type:Number, default:1}
    },
    computed: {
        isDisabled: function () {
            const checkbox = this.$store.getters["csvOption/selectedList"];
            return checkbox.length == 0;
        },
    },
    methods: {
        download: function () {
            this.$store.dispatch("setLoad", true);

            let contents = Contentsfactory.get(this.filename);
            const header = contents.header;
            const card = this.$store.getters.card;
            const checkbox = this.$store.getters["csvOption/selectedList"];
            const filterd = card.filter((c) => {
                return checkbox.includes(c.id);
            });
            const jsonArray = filterd.map((c, index) => contents.contents(c, this.startnum + index));

            const csv = this.$papa.unparse({
                fields: header,
                data: JSON.stringify(jsonArray),
            });
            write(csv, `${this.filename}.csv`);
            this.$store.dispatch("setLoad", false);
            $("#complate").modal("show");
        },
        ok: function () {
            $("#complate").modal("hide");
        },
    },
};
</script>
<style scoped>
#ok {
    margin: 5px auto;
}
</style>
