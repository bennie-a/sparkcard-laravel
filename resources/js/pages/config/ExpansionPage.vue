<template>
    <section>
        <message-area></message-area>
        <div class="ui grid">
            <div class="six wide left floated column mt-1 ui form">
                <div class="field">
                    <label for="">略称(一部でもOK)</label>
                    <div class="ui action input">
                        <input type="text" v-model="keyword" />
                        <button class="ui teal button" @click="search">
                            検索
                        </button>
                    </div>
                </div>
            </div>
            <div
                class="six wide right floated column right aligned bottom aligned content"
            >
                <button class="ui teal basic button" @click="show">
                    新しく登録する
                </button>
            </div>
        </div>
        <div class="ui divider" v-if="$store.getters.card.length != 0"></div>
        <table
            class="ui table striped six column"
            v-if="this.$store.getters.card.length != 0"
        >
            <thead>
                <tr>
                    <th class="five wide">名称</th>
                    <th>略称</th>
                    <th>リリース日</th>
                    <th class="center aligned">カード登録状況</th>
                    <th class="">カード追加</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ex in this.$store.getters.card" :key="ex">
                    <td>{{ ex.name }}</td>
                    <td>{{ ex.attr }}</td>
                    <td>{{ ex.release_date }}</td>
                    <td v-if="ex.is_exist" class="positive center aligned">
                        <i class="bi bi-check-circle-fill"></i>
                    </td>
                    <td v-else class="negative center aligned">
                        <i class="bi bi-x-square-fill"></i>
                    </td>
                    <td>
                        <button
                            class="ui button teal basic"
                            @click="toPostCardPage(ex.name, ex.attr)"
                        >
                            追加する
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
    <now-loading></now-loading>
</template>
<script>
import NowLoading from "../component/NowLoading.vue";
import { AxiosTask } from "../../component/AxiosTask";
import MessageArea from "../component/MessageArea.vue";

export default {
    data() {
        return {
            expansions: null,
            keyword: null,
        };
    },
    mounted: async function () {
        this.$store.dispatch("message/clear");
        this.$store.dispatch("expansion/clear");
    },
    methods: {
        show: function () {
            this.$router.push("/config/expansion/post");
        },
        toPostCardPage: function (setname, attr) {
            console.log(attr);
            this.$router.push({
                name: "PostCardInfo",
                params: { setname: setname, attr: attr },
            });
        },
        search: async function () {
            console.log(this.keyword);
            this.$store.dispatch("clearCards");
            this.$store.dispatch("setLoad", true);
            const query = { params: { query: this.keyword } };
            const success = function (response, store, query) {
                store.dispatch("setCard", response.data);
                // store.dispatch("expansion/result", response.data);
            };
            const fail = function (e, store, query) {
                console.error(e);
                store.dispatch("message/error", "検索結果がありません。");
            };
            const task = new AxiosTask(this.$store);
            await task.get("/database/exp", query, success, fail);

            this.$store.dispatch("setLoad", false);
        },
    },
    components: {
        "now-loading": NowLoading,
        "message-area": MessageArea,
    },
};
</script>
<style scoped>
i {
    font-size: 1.5rem;
}
</style>
