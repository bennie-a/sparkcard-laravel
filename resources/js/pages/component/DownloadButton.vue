<template>
    <v-btn
        variant="flat"
        color="teal-lighten-1"
        @click="download"
        :disabled="isDisabled"
    >
        <v-icon icon="mdi-download"></v-icon>
        <slot></slot>
    </v-btn>
    <v-dialog v-model="dialog">
        <v-card class="text-center mx-auto" width="400">
            <v-card-title>
                <v-icon icon="mdi-information"></v-icon>
                ダウンロードが完了しました。
            </v-card-title>
            <v-card-actions class="d-flex justify-center">
                <v-btn text="閉じる" @click="dialog = false"></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import { write } from "../../composables/CSVWriter";
import Contentsfactory from "../../csv/ContentsFactory";

export default {
    data() {
        return {
            dialog:false
        };
    },
    props: {
        filename: { type: String, reqiured: true },
        startnum:{type:Number, default:1},
        isDisabled:{type:Boolean, default:false},
        card:{type:Object, reqiured:true}
    },
    methods: {
        download: function () {

            let contents = Contentsfactory.get(this.filename);
            const header = contents.header;
            const jsonArray = this.card.map((c, index) => contents.contents(c, this.startnum + index));
            const csv = this.$papa.unparse({
                fields: header,
                data: JSON.stringify(jsonArray),
            });
            write(csv, `${this.filename}.csv`);
            this.dialog = true;
        },
    },
};
</script>
<style scoped>
#ok {
    margin: 5px auto;
}
</style>
