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
            一括登録・更新用CSVを作成する
        </button>
        <download-button color="violet"
            ><i class="download icon"></i
            >登録・更新用CSVを作成する</download-button
        >
        <file-upload @upload="csvUpload" @download="updateDownload"
            >商品一覧をアップロード</file-upload
        >
    </div>
    <card-list></card-list>
    <now-loading></now-loading>
</template>
<script>
import NowLoading from "../component/NowLoading.vue";
import CardList from "../component/CardList.vue";
import MessageArea from "../component/MessageArea.vue";
import { writeCsv, write } from "../../composables/CSVWriter";
import {
    toItemName,
    toSurfaceName,
    toRevName,
    toNoLabelName,
} from "../../composables/CardCollector";
import SearchForm from "../component/SearchForm.vue";
import CSVUpload from "../component/CSVUpload.vue";
import { CSV_HEADERS } from "../../cost/CsvHeader";
import DownloadButton from "../component/DownloadButton.vue";

export default {
    data() {
        return {
            isPrinting: false,
            isPublic: true,
            contentMap: {},
        };
    },
    mounted: function () {
        this.$store.dispatch("search/status", "BASE登録予定");
    },

    methods: {
        csvUpload: function (file) {
            this.$papa.parse(file, {
                header: true,
                complete: function (results) {
                    let csvdata = results.data;
                    csvdata.map((line) => {
                        this.contentMap[line["商品名"]] = line["商品ID"];
                    });
                    const card = this.$store.getters.card;
                    card.map((c) => {
                        let code = this.contentMap[toItemName(c)];
                        if (code !== undefined) {
                            c["baseId"] = code;
                        }
                    });
                }.bind(this),
            });
            console.log(file.name);
        },
        downloadItem: function () {
            this.$store.dispatch("setLoad", true);

            const fields = CSV_HEADERS.base;
            const card = this.$store.getters.card;
            let jsonArray = card.map((c) => {
                let showIndex = c.exp.orderId * 10 + c.index;
                let json = [
                    c.baseId,
                    toItemName(c),
                    "",
                    "",
                    "",
                    c.price,
                    "1",
                    c.stock,
                    this.isPublic ? 1 : 0,
                    showIndex,
                    "",
                    toSurfaceName(c),
                    toNoLabelName(c),
                    toRevName(c),
                    "",
                ];
                return json;
            });

            const csv = this.$papa.unparse({
                fields: fields,
                data: JSON.stringify(jsonArray),
            });
            write(csv, "base-item.csv");
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
        "file-upload": CSVUpload,
        "download-button": DownloadButton,
    },
};
</script>
