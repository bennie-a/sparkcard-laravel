<template>
    <message-area></message-area>
    <search-form limitprice="50" status="ショップ登録予定"></search-form>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <div class="mt-1 ui form">
            <div class="four fields">
                <div class="mr-1 two wide columns field">
                    <label for="">登録開始No.</label>
                    <input type="number" step="1" min="1" v-model="start">
                </div>
                <div class="three wide columns field">
                    <label for="">公開/非公開</label>
                    <div class="ui toggle checkbox mr-2">
                        <input type="checkbox" name="public" v-model="isPublic" />
                        <label>商品を公開する</label>
                    </div>
                </div>
                <div class="three wide columns field">
                    <label for="">画像名</label>
                    <div class="ui toggle checkbox mr-2">
                    <input type="checkbox" name="public" v-model="isPrinting" />
                    <label for="name">Now Printing</label>
                </div>
                </div>
            </div>
            <download-button filename="base_item"
                :startnum="start"><i class="download icon"></i
                >ダウンロード
</download-button
            >
        </div>
    </div>
    <card-list exp isNotion></card-list>
    <now-loading></now-loading>
</template>
<script>
import NowLoading from "../component/NowLoading.vue";
import CardList from "../component/CardList.vue";
import MessageArea from "../component/MessageArea.vue";
import { writeCsv, write } from "../../composables/CSVWriter";
import SearchForm from "../component/SearchForm.vue";
import CSVUpload from "../component/CSVUpload.vue";
import DownloadButton from "../component/DownloadButton.vue";

export default {
    components: {
        "now-loading": NowLoading,
        "card-list": CardList,
        "message-area": MessageArea,
        "search-form": SearchForm,
        "file-upload": CSVUpload,
        "download-button": DownloadButton,
    },
    
    data() {
        return {
            isPrinting: false,
            isPublic: true,
            canCategory: true,
            contentMap: {},
            start:1
        };
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
                    this.canCategory = false;
                }.bind(this),
            });
            console.log(file.name);
        },
    },

};
</script>
