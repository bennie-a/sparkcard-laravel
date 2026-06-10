<template>
    <message-area></message-area>
    <search-form limitprice="50" status="ショップ登録予定"></search-form>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <div class="ui toggle checkbox mr-2">
            <input type="checkbox" name="public" v-model="isPublic" />
            <label>メルカリに公開する</label>
        </div>
        <download-button filename="mercari_item"
            ><i class="download icon"></i
            >登録・更新用CSVを作成する</download-button
        >
    </div>
    <card-list exp isNotion></card-list>
</template>
<script>
import CardList from "../component/CardList.vue";
import MessageArea from "../component/msg/MessageArea.vue";
import SearchForm from "../component/SearchForm.vue";
import CSVUpload from "../component/CSVUpload.vue";
import DownloadButton from "../component/DownloadButton.vue";

export default {
    components: {
        "card-list": CardList,
        "message-area": MessageArea,
        "search-form": SearchForm,
        "download-button": DownloadButton,
    },
    data() {
        return {
            isPublic: true,
            // contentMap: {},
        };
    },
    methods: {},

};
</script>
