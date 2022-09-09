<template>
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
</template>
<script>
import { AxiosTask } from "../../component/AxiosTask";

export default {
    data() {
        return {
            expansion: "",
        };
    },
    methods: {
        async search() {
            console.log("Notion Card Search...");
            this.$store.dispatch("setLoad", true);
            this.$store.dispatch("clearCards");
            const task = new AxiosTask(this.$store);
            console.log(this.$store.getters["search/status"]);
            const query = {
                expansion: this.expansion,
            };
            const success = function (response, store, query) {
                let results = response.data;
                console.log("Card Get Count " + results.length);
                store.dispatch("setCard", results);
                store.dispatch(
                    "setSuccessMessage",
                    results.length + "件取得しました。"
                );
            };
            const fail = function (e, store, query) {
                const res = e.response;
                console.log(res.status);
                console.log(res.data);
                store.dispatch("message/error", res.data.message);
            };
            const status = this.$store.getters["search/status"];
            await task.get(
                "/notion/card?status=" + status,
                query,
                success,
                fail
            );
        },
    },
};
</script>
