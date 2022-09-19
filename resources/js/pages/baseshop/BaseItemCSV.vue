<template>
    <message-area></message-area>
    <search-form limitprice="50"></search-form>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <div class="ui toggle checkbox mr-2">
            <input type="checkbox" name="public" v-model="isPrinting" />
            <label for="name">画像に「Now Printing」を使用する</label>
        </div>
        <div class="ui toggle checkbox mr-2">
            <input type="checkbox" name="public" v-model="isPublic" />
            <label>BASEに公開する</label>
        </div>
        <download-button color="violet" filename="base_item"
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
import DownloadButton from "../component/DownloadButton.vue";
import { NOTION_STATUS } from "../../cost/NotionStatus";

export default {
    data() {
        return {
            isPrinting: false,
            isPublic: true,
            contentMap: {},
        };
    },
    mounted: function () {
        this.$store.dispatch("search/status", NOTION_STATUS.tobase);
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
