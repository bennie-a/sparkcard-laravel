<template>
    <section>
        <message-area></message-area>
        <div class="ui grid">
            <div class="six wide left floated column mt-1 ui form">
                <div class="field">
                    <label for="">名称or略称(一部でもOK)</label>
                    <div class="ui action input">
                        <input type="text" />
                        <button class="ui teal button">検索</button>
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
        <div
            class="ui divider"
            v-if="$store.getters['expansion/result'].length != 0"
        ></div>
        <table
            class="ui table striped six column"
            v-if="$store.getters['expansion/result'].length != 0"
        >
            <thead>
                <tr>
                    <th class="six wide">名称</th>
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
            release_date: new Date(),
        };
    },
    mounted: async function () {
        this.$store.dispatch("message/clear");
        this.$store.dispatch("expansion/clear");
        // this.$store.dispatch("setLoad", true);
        // const task = new AxiosTask(this.$store);
        // const success = function (response, store, query) {
        //     if (response.status == 201) {
        //         store.dispatch("expansion/setResult", response.data);
        //     }
        // };
        // const fail = function (e, store, query) {};
        // await task.get("/notion/expansion/", [], success, fail);
        // this.$store.dispatch("setLoad", false);
    },
    methods: {
        show: function () {
            this.$router.push("/settings/expansion/post");
        },
        store: async function () {
            const list = this.$store.getters["expansion/result"];
            const task = new AxiosTask(this.$store);
            await Promise.all(
                list.map(async (exp) => {
                    let json = {
                        id: exp.id,
                        name: exp.name,
                        attr: exp.attr,
                        base_id: exp.base_id,
                        release_date: exp.release_date,
                    };
                    const success = function (response, store) {};
                    await task.post("/database/exp", json, success);
                })
            );
            this.$store.dispatch(
                "setSuccessMessage",
                `${list.length}件登録が完了しました。`
            );
        },
    },
    components: {
        "now-loading": NowLoading,
        "message-area": MessageArea,
    },
};
</script>
