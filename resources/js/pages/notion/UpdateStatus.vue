<template>
    <div class="ui segment">
        <h2 class="ui small header">
            <i class="question circle icon"></i>絞り込み条件
        </h2>
        <div class="flex">
            <select class="ui dropdown" v-model="status">
                <option value="ロジクラ要登録">ロジクラ要登録</option>
                <option value="販売保留">販売保留</option>
            </select>
            <div class="ui right labeled input">
                <input
                    type="text"
                    placeholder="価格"
                    v-model="price"
                    @input="
                        (event) =>
                            (value = event.target.value.replace(/[^0-9]/g, ''))
                    "
                />
                <div class="ui basic label">円</div>
            </div>
            <select class="ui dropdown" v-model="isMore">
                <option value="true">以上</option>
                <option value="false">未満</option>
            </select>
            <button class="ui purple button ml-1" @click="search">
                検索する
            </button>
        </div>
    </div>
    <message-area></message-area>
    <div class="ui negative message" v-if="error != ''">
        <div class="header">
            {{ error }}
        </div>
        <p></p>
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
            // cards: [],
            status: "ロジクラ要登録",
            price: "",
            isMore: false,
            error: "",
        };
    },
    methods: {
        search() {
            console.log("Notion Card Search...");
            console.log(this.status);
            this.$store.dispatch("setLoad", true);
            this.$store.dispatch("clearCards");
            const task = new AxiosTask(this.$store);
            const query = {
                status: this.status,
                price: Number(this.price),
                isMore: Boolean(this.isMore),
            };
            const success = function (response, $store, query) {
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
            task.get(
                "/notion/card?status=ロジクラ要登録",
                query,
                success,
                fail
            );
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
