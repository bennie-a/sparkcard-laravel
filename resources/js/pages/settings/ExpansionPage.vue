<template>
    <section>
        <h2>未登録分</h2>
        <button class="ui button purple">登録する</button>
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

export default {
    data() {
        return {
            expansions: null,
        };
    },
    mounted: async function () {
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
    components: {
        "now-loading": NowLoading,
    },
};
</script>
