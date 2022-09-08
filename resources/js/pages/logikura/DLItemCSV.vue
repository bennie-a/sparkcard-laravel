<template>
    <message-area></message-area>
    <div class="ui form mt-2 segment">
        <div class="three fields">
            <div class="ui action input field">
                <input
                    type="text"
                    placeholder="エキスパンション(完全一致)"
                    v-model="expansion"
                />
                <button class="ui primary button">検索</button>
            </div>
        </div>
    </div>
    <div class="mt-3" v-if="$store.getters.cardsLength != 0">
        <button class="ui purple button" @click="">ダウンロードする</button>
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
            selectedStatus: "ロジクラ要登録",
            updateStatus: "ロジクラ要登録",
            expansion: "",
            isMore: false,
            error: "",
        };
    },
    mounted: function () {
        this.$store.dispatch("clearCards");
        this.$store.dispatch("clearMessage");
        console.log("update status mounted");
    },

    methods: {
        async search() {
            console.log("Notion Card Search...");
            console.log(this.selectedStatus);
            this.$store.dispatch("setLoad", true);
            this.$store.dispatch("clearCards");
            const task = new AxiosTask(this.$store);
            const query = {
                status: this.selectedStatus,
                price: Number(this.price),
                isMore: Boolean(this.isMore),
            };
            const success = function (response, $store, query) {
                console.log(query.status);
                let results = [];
                if (results.error) {
                }
                if (query.price > 0) {
                    results = response.data.filter((r) => {
                        if (query.isMore) {
                            return r.price >= query.price;
                        } else {
                            return r.price < query.price;
                        }
                    });
                } else {
                    results = response.data;
                }
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
                "/notion/card?status=" + this.selectedStatus,
                query,
                success,
                fail
            );
        },
        async update() {
            this.$store.dispatch("setLoad", true);
            console.log("Status Update...");
            console.log(this.updateStatus);
            const task = new AxiosTask(this.$store);
            const success = function (response, query) {
                const result = response.data;
            };
            const fail = function (e, query) {
                console.error(e);
            };
            const card = this.$store.getters.card;
            await Promise.all(
                card.map(async (c) => {
                    let url = "/notion/card/" + c.id;
                    let query = { status: this.updateStatus };
                    await task.patch(url, query, success, fail);
                })
            );
            this.$store.dispatch("setSuccessMessage", "更新が完了しました。");
            this.store.dispatch("setLoad", false);
        },
    },
    watch: {
        price: function () {
            const pattern = /[a-zA-Z]/g; // 半角英字のみ不可
            this.price = this.price.replace(pattern, "");
        },
    },
    components: {
        "now-loading": NowLoading,
        "card-list": CardList,
        "message-area": MessageArea,
    },
};
</script>
