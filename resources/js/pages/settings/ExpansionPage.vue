<template>
    <section>
        <message-area></message-area>
        <h2 class="ui dividing header">未登録分</h2>
        <ModalButton @action="store">DBに登録する</ModalButton>
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
import ModalButton from "../component/ModalButton.vue";
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
        };
        const fail = function (e, store, query) {};
        await task.get("/notion/expansion/", [], success, fail);
        this.$store.dispatch("setLoad", false);
    },
    methods: {
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
        ModalButton: ModalButton,
    },
};
</script>
