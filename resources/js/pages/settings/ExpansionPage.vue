<template>
    <section>
        <message-area></message-area>
        <h2>未登録分</h2>
        <button class="ui button purple" @click="showRegist">
            DBに登録する
        </button>
        <div id="regist" class="ui tiny modal">
            <div class="header">Notice</div>
            <div class="content" v-if="this.$store.getters.isLoad == false">
                登録してもよろしいですか?
            </div>
            <div class="actions" v-if="this.$store.getters.isLoad == false">
                <button class="ui cancel button">
                    <i class="close icon"></i>キャンセル
                </button>
                <button class="ui primary button" @click="store">
                    <i class="checkmark icon"></i>登録する
                </button>
            </div>
            <now-loading></now-loading>
        </div>

        <table class="ui table striped six column">
            <thead>
                <tr>
                    <th>名称</th>
                    <th>略称</th>
                    <th>BASEID</th>
                    <th>リリース日</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ex in $store.getters['expansion/result']" :key="ex">
                    <td>{{ ex.name }}</td>
                    <td>{{ ex.attr }}</td>
                    <td>{{ ex.base_id }}</td>
                    <td>{{ ex.release_date }}</td>
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
        };
    },
    mounted: async function () {
        this.$store.dispatch("message/clear");
        this.$store.dispatch("expansion/clear");
        this.$store.dispatch("setLoad", true);
        const task = new AxiosTask(this.$store);
        const success = function (response, store, query) {
            store.dispatch("expansion/setResult", response.data);
            // this.expansions = response.data;
        };
        const fail = function (e, store, query) {};
        await task.get("/notion/expansion/", [], success, fail);
        this.$store.dispatch("setLoad", false);
    },
    methods: {
        showRegist() {
            $("#regist").modal("show");
        },

        store: async function () {
            this.$store.dispatch("setLoad", true);

            let exp = this.$store.getters["expansion/result"][0];
            let json = {
                id: exp.id,
                name: exp.name,
                attr: exp.attr,
                base_id: exp.base_id,
                release_date: exp.release_date,
            };
            const task = new AxiosTask(this.$store);
            const success = function (response, store) {};
            await task.post("/database/exp", json, success);
            this.$store.dispatch("setLoad", false);
            $("#regist").modal("hide");
            this.$store.dispatch("setSuccessMessage", "登録が完了しました。");
        },
    },
    components: {
        "now-loading": NowLoading,
        "message-area": MessageArea,
    },
};
</script>
