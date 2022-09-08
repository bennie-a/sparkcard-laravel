<template>
    <message-area></message-area>
    <div class="ui form mt-2 segment">
        <div class="three fields">
            <div class="ui action input field">
                <input
                    type="text"
                    placeholder="エキスパンション(未実装)"
                    v-model="expansion"
                />
                <button class="ui primary button" @click="search">検索</button>
            </div>
        </div>
    </div>
    <div class="mt-2">
        <div class="ui toggle checkbox mr-2">
            <input type="checkbox" name="public" v-model="isPrinting" />
            <label for="name">画像に「Now Printing」を使用する</label>
        </div>
        <div class="ui buttons float right">
            <button class="ui violet button" @click="downloadLogikura">
                ロジクラ用CSVをダウンロードする
            </button>
            <div class="or"></div>
            <button class="ui pink button">BASE用CSVをダウンロードする</button>
        </div>
    </div>
    <card-list></card-list>
    <now-loading></now-loading>
</template>
<style scoped>
div.flex {
    display: flex;
    align-items: center;
    column-gap: 1rem;
}
</style>
<script>
import { AxiosTask } from "../../component/AxiosTask";
import NowLoading from "../component/NowLoading.vue";
import CardList from "../component/CardList.vue";
import MessageArea from "../component/MessageArea.vue";
export default {
    data() {
        return {
            expansion: "",
            isPrinting: false,
            error: "",
        };
    },
    mounted: function () {
        this.$store.dispatch("clearCards");
        this.$store.dispatch("clearMessage");
        console.log("csv mounted");
    },

    methods: {
        async search() {
            console.log("Notion Card Search...");
            this.$store.dispatch("setLoad", true);
            this.$store.dispatch("clearCards");
            const task = new AxiosTask(this.$store);
            const query = {
                status: this.selectedStatus,
                expansion: this.expansion,
            };
            const success = function (response, $store, query) {
                console.log(query.expansion);
                let results = response.data;
                console.log("Card Get Count " + results.length);
                $store.dispatch("setCard", results);
                $store.dispatch(
                    "setSuccessMessage",
                    results.length + "件取得しました。"
                );
            };
            const fail = function (e, $store, query) {
                const res = e.response;
                this.error = res.code;
                console.log(res.status);
            };
            await task.get(
                "/notion/card?status=ロジクラ要登録",
                query,
                success,
                fail
            );
        },
        downloadLogikura() {
            this.$store.dispatch("setLoad", true);
            console.log(this.isPrinting);
            this.$store.dispatch("setLoad", false);
        },
    },
    components: {
        "now-loading": NowLoading,
        "card-list": CardList,
        "message-area": MessageArea,
    },
};
</script>
