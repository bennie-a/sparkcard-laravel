<template>
    <message-area></message-area>
    <search-form limitprice="0"></search-form>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <download-button filename="logikura_item"
            ><i class="download icon"></i
            >商品登録用ファイルを作成する</download-button
        >
        <download-button color="pink" filename="logikura_stock"
            ><i class="download icon"></i
            >在庫ファイルを作成する</download-button
        >
    </div>
    <card-list exp isNotion></card-list>
    <now-loading></now-loading>
</template>

<script>
import NowLoading from "../component/NowLoading.vue";
import CardList from "../component/CardList.vue";
import MessageArea from "../component/MessageArea.vue";
import SearchForm from "../component/SearchForm.vue";
import DownloadButton from "../component/DownloadButton.vue";
import { NOTION_STATUS } from "../../cost/NotionStatus";

export default {
    data() {
        return {};
    },
    mounted: function () {
        this.$store.dispatch("search/status", NOTION_STATUS.logikura);
    },
    computed: {
        canStock() {
            return Object.keys(this.barcodeMap).length > 0;
        },
    },
    methods: {},
    components: {
        "now-loading": NowLoading,
        "card-list": CardList,
        "message-area": MessageArea,
        "search-form": SearchForm,
        "download-button": DownloadButton,
    },
};
</script>
