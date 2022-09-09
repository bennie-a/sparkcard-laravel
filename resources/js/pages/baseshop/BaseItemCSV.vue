<template>
    <message-area></message-area>
    <search-form></search-form>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <div class="ui toggle checkbox mr-2">
            <input type="checkbox" name="public" v-model="isPrinting" />
            <label for="name">画像に「Now Printing」を使用する</label>
        </div>
        <button class="ui violet button" @click="downloadLogikura">
            ロジクラ用CSVをダウンロードする
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
export default {
    mounted: function () {
        this.$store.dispatch("clearCards");
        this.$store.dispatch("clearMessage");
        console.log("csv mounted");

        this.$store.dispatch("search/status", "BASE登録予定");
    },

    methods: {
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
