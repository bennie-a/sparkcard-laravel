<template>
    <message-area></message-area>
    <section class="mt-1">
        <file-upload @action="upload" type="json"></file-upload>
        {{ filename }}
    </section>
    <section class="wall mt-1">
        <ModalButton @action="store">DBに登録する</ModalButton>
        <form class="ui large form mt-2" v-if="$store.getters.isLoad == false">
            <div class="inline field">
                <label>エキスパンション名：</label>{{ setCode }}
            </div>
            <div class="field">
                <table class="ui table striped six column">
                    <thead>
                        <tr>
                            <th class="one wide">カード番号</th>
                            <th class="four wide">カード名</th>
                            <th class="four wide">英名</th>
                            <th class="one wide">色</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="card in getCards">
                            <td>{{ card.number }}</td>
                            <td>
                                {{ card.name
                                }}<label v-if="card.promotype != ''"
                                    >≪{{ card.promotype }}≫</label
                                >
                            </td>
                            <td>{{ card.en_name }}</td>
                            <td>
                                <label
                                    class="ui large label"
                                    :class="colorlabel(card.color)"
                                    >{{ colortext(card.color) }}</label
                                >
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="this.$store.getters.cardsLength != 0">
                        <tr>
                            <th colspan="4">
                                <pagination></pagination>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>
        <now-loading></now-loading>
    </section>
</template>
<script>
import NowLoading from "../component/NowLoading.vue";
import FileUpload from "../component/FileUpload.vue";
import MessageArea from "../component/MessageArea.vue";
import ListPagination from "../component/ListPagination.vue";
import ModalButton from "../component/ModalButton.vue";
import { AxiosTask } from "../../component/AxiosTask";

import axios from "axios";
export default {
    data() {
        return { filename: "ファイルを選択してください", setCode: "" };
    },
    mounted: function () {
        // this.$store.dispatch("setLoad", true);
    },
    computed: {
        getCards: function () {
            return this.$store.getters.sliceCard;
        },
        colortext: function () {
            return function (key) {
                const colors = {
                    W: "白",
                    B: "黒",
                    U: "青",
                    R: "赤",
                    G: "緑",
                    M: "多色",
                    L: "無色",
                    A: "アーティファクト",
                    Land: "土地",
                };
                return colors[key];
            };
        },
        colorlabel: function () {
            return function (key) {
                const colors = {
                    W: "",
                    B: "black",
                    U: "blue",
                    R: "red",
                    G: "green",
                    M: "orange",
                    L: "grey",
                    A: "grey",
                    Land: "brown",
                };
                return colors[key];
            };
        },
    },
    methods: {
        upload: async function (file) {
            this.$store.dispatch("setLoad", true);
            this.filename = file.name;
            const config = {
                headers: {
                    "Content-Type": "application/json",
                },
            };
            await axios
                .post("/api/upload/card", file, config)
                .then((response) => {
                    if (response.status == 201) {
                        let item = response.data;
                        this.setCode = item.setCode;
                        this.$store.dispatch("setCard", item.cards);
                    }
                })
                .catch((e) => {
                    if (e.response.status == 422) {
                        const errors = e.response.data.errors;
                        let msgs = "<ul>";
                        for (let key in errors) {
                            console.log(key);
                            let array = errors[key];
                            array.forEach((msg) => {
                                msgs += `<li>${msg}</li>`;
                            });
                        }
                        msgs += "</ul>";
                        this.$store.dispatch("message/errorhtml", msgs);
                    }
                })
                .finally(() => {
                    this.$store.dispatch("setLoad", false);
                });
        },
        store: async function () {
            const task = new AxiosTask(this.$store);
            const list = this.$store.getters.card;
            await Promise.all(
                list.map(async (card) => {
                    const success = function (response, store) {};
                    await task.post("/database/card", card, success);
                })
            );
            this.$store.dispatch(
                "setSuccessMessage",
                `${list.length}件登録が完了しました。`
            );

            console.log("store finished.");
        },
    },
    components: {
        "file-upload": FileUpload,
        "message-area": MessageArea,
        "now-loading": NowLoading,
        pagination: ListPagination,
        ModalButton: ModalButton,
    },
};
</script>
<style>
.wall {
    background-color: white;
    padding: 1em;
}
</style>
