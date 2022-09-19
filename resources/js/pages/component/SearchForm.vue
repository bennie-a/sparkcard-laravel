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
import ExpansionStorage from "../../firestore/ExpansionStorage";

export default {
    props: ["limitprice"],
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
                limitprice: this.limitprice,
            };
            const success = async function (response, store, query) {
                console.log(response.status);
                let results = response.data;
                let cards = results.filter((r) => {
                    return r.price >= query.limitprice;
                });
                const storage = new ExpansionStorage();
                await Promise.all(
                    cards.map(async (r) => {
                        const doc = await storage.findById(r.expansion);
                        let exp = {};
                        if (doc == undefined) {
                            exp["name"] = "不明";
                            exp["attr"] = "";
                            exp["orderId"] = "";
                        } else {
                            exp["name"] = doc.name;
                            exp["attr"] = doc.attr;
                            exp["orderId"] = doc.order_id;
                        }
                        r["exp"] = exp;
                    })
                );
                console.log("Card Get Count " + cards.length);
                store.dispatch("setCard", cards);
                store.dispatch(
                    "setSuccessMessage",
                    results.length + "件取得しました。"
                );
            };
            const fail = function (e, store, query) {
                const res = e.response;
                console.error(e);
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
