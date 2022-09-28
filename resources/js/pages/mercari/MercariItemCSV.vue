<template>
    <message-area></message-area>
    <search-form limitprice="300"></search-form>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <div class="ui toggle checkbox mr-2">
            <input type="checkbox" name="public" v-model="isPublic" />
            <label>メルカリに公開する</label>
        </div>
        <download-button color="violet" filename="mercari_item"
            ><i class="download icon"></i
            >登録・更新用CSVを作成する</download-button
        >
    </div>
    <card-list></card-list>
    <now-loading></now-loading>
</template>
<script>
import NowLoading from "../component/NowLoading.vue";
import CardList from "../component/CardList.vue";
import MessageArea from "../component/MessageArea.vue";
import SearchForm from "../component/SearchForm.vue";
import CSVUpload from "../component/CSVUpload.vue";
import DownloadButton from "../component/DownloadButton.vue";
import { NOTION_STATUS } from "../../cost/NotionStatus";

export default {
    data() {
        return {
            isPublic: true,
            // contentMap: {},
        };
    },
    mounted: function () {
        this.$store.dispatch("search/status", NOTION_STATUS.baseonly);
    },
    methods: {},
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
