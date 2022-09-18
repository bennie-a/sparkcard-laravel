<template>
    <message-area></message-area>
    <search-form></search-form>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <download-button filename="logikura_item"
            ><i class="download icon"></i
            >商品登録用ファイルを作成する</download-button
        >

        <button class="ui pink button" @click="toggle">
            在庫ファイルを作成する
        </button>
        <div id="upload" class="ui mini modal">
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
        <div id="download" class="ui mini modal">
            <i class="close icon"></i>
            <div class="header">在庫ファイルをダウンロードしますか?</div>
            <div class="content text-center">
                <button class="ui primary button" @click="downloadStockFile">
                    OK
                </button>
                <button class="ui button" @click="downloadCancel">
                    キャンセル
                </button>
            </div>
        </div>
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
import DownloadButton from "../component/DownloadButton.vue";
import { NOTION_STATUS } from "../../cost/NotionStatus";

export default {
    data() {
        return {
            barcodeMap: {},
        };
    },
    mounted: function () {
        this.$store.dispatch("search/status", NOTION_STATUS.logikura);
    },
    computed: {
        canStock() {
            return Object.keys(this.barcodeMap).length > 0;
        },
    },
    methods: {
        toggle: function () {
            $("#upload").modal("show");
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

            this.$papa.parse(file, {
                header: true,
                complete: function (results) {
                    let csvdata = results.data;
                    // const barcodeMap = {};
                    csvdata.map((line) => {
                        this.barcodeMap[line["商品名"]] = line["バーコード"];
                    });
                }.bind(this),
            });
        },

        downloadCancel: function () {
            $("#download").modal("hide");
        },
        downloadStockFile: function () {
            const card = this.$store.getters.card.filter((c) => {
                if (c.barcode !== undefined && c.stock > 0) {
                    return c;
                }
            });
            let header = "バーコード,在庫数\n";
            let pushfn = (array, c, index) => {
                array.push(c.barcode);
                array.push(c.stock);
            };
            writeCsv(header, card, "logikura_stock", pushfn);
            this.$store.dispatch(
                "setSuccessMessage",
                "在庫ファイルを作成しました。"
            );
            this.downloadCancel();
        },
    },
    components: {
        "now-loading": NowLoading,
        "card-list": CardList,
        "message-area": MessageArea,
        "search-form": SearchForm,
        "download-button": DownloadButton,
    },
    watch: {
        barcodeMap: {
            handler: function () {
                const card = this.$store.getters.card;
                card.map((c) => {
                    let barcode = this.barcodeMap[toItemName(c)];
                    if (barcode !== undefined) {
                        c["barcode"] = barcode;
                    }
                });
                $("#upload").modal("hide");
                $("#download").modal("show");
            },
            deep: true,
        },
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
