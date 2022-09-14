<template>
    <message-area></message-area>
    <search-form></search-form>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <div class="ui toggle checkbox mr-2">
            <input type="checkbox" name="public" v-model="isPrinting" />
            <label for="name">画像に「Now Printing」を使用する</label>
        </div>
        <div class="ui toggle checkbox mr-2">
            <input type="checkbox" name="public" v-model="isPublic" />
            <label>BASEに公開する</label>
        </div>
        <button class="ui violet button" @click="downloadItem">
            商品登録用CSVをダウンロードする
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
import {
    toItemName,
    toRevName,
    createTemplate,
} from "../../composables/CardCollector";
import SearchForm from "../component/SearchForm.vue";
export default {
    data() {
        return {
            isPrinting: false,
            isPublic: true,
        };
    },
    mounted: function () {
        this.$store.dispatch("search/status", "BASE登録予定");
    },

    methods: {
        downloadItem: function () {
            this.$store.dispatch("setLoad", true);
            let header =
                "商品名,説明,価格,税率,在庫数,公開状態,表示順,種類在庫数,画像1,画像2\n";
            let pushFn = (array, c, index) => {
                array.push(toItemName(c));
                array.push("");
                array.push(c.price);
                array.push("1");
                array.push(c.stock);
                array.push(this.isPublic ? 1 : 0);
                array.push(index + 1);
                array.push(c.stock);
                if (this.isPrinting) {
                    array.push("Now-Printing.jpg");
                } else {
                    array.push(toSurfaceName(c.enName));
                    array.push(toRevName(c.enName));
                }
            };

            writeCsv(header, this.$store.getters.card, "base-item.csv", pushFn);
            this.$store.dispatch(
                "setSuccessMessage",
                "CSVのダウンロードが完了しました。"
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
