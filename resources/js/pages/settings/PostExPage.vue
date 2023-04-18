<template>
    <section class="ui grids">
        <message-area></message-area>
        <form class="content ui form">
            <div class="two fields">
                <div class="required six wide field">
                    <label for="">名称</label>
                    <input type="text" />
                </div>
                <div class="required two wide field">
                    <label for="">略称</label>
                    <input type="text" />
                </div>
            </div>
            <div class="two fields">
                <div class="required four wide field">
                    <label for="">ブロック</label>
                    <select v-model="block" class="ui search dropdown">
                        <option value="">ミラディン</option>
                        <option value="">神河</option>
                        <option value="">ドミナリア</option>
                        <option value="">ゼンディカー</option>
                        <option value="">イコリア</option>
                        <option value="">エルドレイン</option>
                        <option value="">カラデシュ</option>
                        <option value="">基本セット</option>
                        <option value="">その他</option>
                    </select>
                </div>
                <div class="required four wide field">
                    <label for="">フォーマット</label>
                    <select v-model="format" class="ui search dropdown">
                        <option>スタンダード</option>
                        <option>パイオニア</option>
                        <option>モダン</option>
                        <option>レガシー</option>
                        <option>統率者</option>
                        <option>マスターピース</option>
                        <option>その他</option>
                    </select>
                </div>
            </div>
            <div class="eight wide field">
                <label for="">発売日</label>
                <Datepicker
                    v-model="release_date"
                    locale="jp"
                    input-class-name="pl-2"
                    auto-apply
                    :format="release_format"
                    :enable-time-picker="false"
                ></Datepicker>
            </div>
            <button class="ui teal button">
                <i class="checkmark icon"></i>登録する
            </button>
        </form>
        <div class="actions"></div>
    </section>
</template>
<script>
import { AxiosTask } from "../../component/AxiosTask";
import MessageArea from "../component/MessageArea.vue";
import Datepicker from "@vuepic/vue-datepicker";

export default {
    data() {
        return {
            name: "",
            attr: "",
            block: "",
            format: "スタンダード",
            release_date: null,
        };
    },
    methods: {
        release_format: function (date) {
            const day = date.getDate();
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            return `${year}/${month}/${day}`;
        },
        store: async function () {
            const list = this.$store.getters["expansion/result"];
            const task = new AxiosTask(this.$store);
            await Promise.all(
                list.map(async (exp) => {
                    let json = {
                        name: this.name,
                        attr: this.attr,
                        block: this.block,
                        format: this.format,
                        release_date: this.release_date,
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
        "message-area": MessageArea,
        Datepicker: Datepicker,
    },
};
</script>
<style></style>
