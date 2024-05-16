<template>
    <section class="ui grids">
        <message-area></message-area>
        <router-link to="/config/expansion"
            ><i class="bi bi-arrow-left"></i>一覧に戻る</router-link
        >
        <div class="mt-1 content ui form">
            <div class="two fields">
                <div class="required six wide field">
                    <label for="">名称</label>
                    <input type="text" v-model="name" />
                </div>
                <div class="required two wide field">
                    <label for="">略称</label>
                    <input type="text" v-model="attr" />
                </div>
            </div>
            <div class="two fields">
                <div class="required four wide field">
                    <label for="">ブロック</label>
                    <select v-model="block" class="ui search dropdown">
                        <option>サンダー・ジャンクション</option>
                        <option>ミラディン/ファイレクシア</option>
                        <option>神河</option>
                        <option>ドミナリア</option>
                        <option>ラヴニカ</option>
                        <option>ゼンディカー</option>
                        <option>イコリア</option>
                        <option>エルドレイン</option>
                        <option>イクサラン</option>
                        <option>ストリクスヘイヴン</option>
                        <option>イニストラード</option>
                        <option>カラデシュ</option>
                        <option>インベイジョン</option>
                        <option>基本セット</option>
                        <option>その他</option>
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
            <ModalButton @action="store"
                ><i class="checkmark icon"></i>登録する</ModalButton
            >
        </div>
    </section>
</template>
<script>
import { AxiosTask } from "../../component/AxiosTask";
import MessageArea from "../component/MessageArea.vue";
import Datepicker from "@vuepic/vue-datepicker";
import ModalButton from "../component/ModalButton.vue";

export default {
    data() {
        return {
            name: "",
            attr: "",
            block: "サンダー・ジャンクション",
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
        back: function () {
            this.$router.push("/config/expansion");
        },
        store: function () {
            const task = new AxiosTask(this.$store);
            let json = {
                name: this.name,
                attr: this.attr,
                block: this.block,
                format: this.format,
                release_date: this.release_date,
            };
            const success = function (response, store) {
                // this.back();
            };
            task.post("/database/exp", json, success);
            this.$store.dispatch(
                "setSuccessMessage",
                `${this.name}を登録しました！`
            );
        },
    },
    components: {
        "message-area": MessageArea,
        Datepicker: Datepicker,
        ModalButton: ModalButton,
    },
};
</script>
<style></style>
