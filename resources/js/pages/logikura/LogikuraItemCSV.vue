<template>
    <message-area></message-area>
    <search-form></search-form>
    <div class="ui mini modal">
        <i class="close icon"></i>
        <div class="header">商品ファイルをアップロード</div>
        <div class="content">
            <input
                type="file"
                class="inputfile"
                id="embedpollfileinput"
                @change="onFileUpload"
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

    <button class="ui pink button" @click="toggle">
        在庫ファイルを作成する
    </button>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <button class="ui violet button" @click="downloadLogikura">
            商品登録用ファイルを作成する
        </button>
    </div>
    <card-list></card-list>
    <now-loading></now-loading>
</template>

<script>
import NowLoading from "../component/NowLoading.vue";
import CardList from "../component/CardList.vue";
import MessageArea from "../component/MessageArea.vue";
import { writeCsv } from "../../composables/CSVWriter";
import { toItemName } from "../../composables/CardCollector";
import SearchForm from "../component/SearchForm.vue";
import Encoding from "encoding-japanese";
import fs from "fs";
export default {
    mounted: function () {
        this.$store.dispatch("search/status", "ロジクラ要登録");
    },

    methods: {
        toggle: function () {
            $(".mini.modal").modal("show");
        },
        onFileUpload: function (e) {
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
            // let reader = new FileReader();
            // reader.readAsText(file);
            // reader.onload = (e) => {
            //     let encoding = Encoding.detect(e.target.result);
            //     let unicodestring = Encoding.convert(e.target.result, {
            //         to: "UTF8",
            //         from: "ASCII",
            //         type: "string",
            //     });
            // };
            this.$papa.parse(file, {
                header: true,
                complete: function (results) {
                    let csvdata = results.data;
                    console.log(csvdata);
                },
            });

            $(".mini.modal").modal("hide");
        },
        downloadLogikura: function () {
            this.$store.dispatch("setLoad", true);
            let header =
                "カテゴリ,商品名,種類名,種類画像,販売価格,仕入価格,税率\n";
            let pushFn = (array, c, index) => {
                array.push("団結のドミナリア");
                array.push(toItemName(c));
                array.push("NM");
                array.push(c.image);
                array.push(c.price);
                array.push("23");
                array.push("10");
            };

            writeCsv(
                header,
                this.$store.getters.card,
                "logikura-item.csv",
                pushFn
            );
            this.$store.dispatch(
                "setSuccessMessage",
                "ロジクラ用CSVのダウンロードが完了しました。"
            );

            this.$store.dispatch("setLoad", false);
        },
    },
    components: {
        "now-loading": NowLoading,
        "card-list": CardList,
        "message-area": MessageArea,
        "search-form": SearchForm,
    },
};
</script>
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
