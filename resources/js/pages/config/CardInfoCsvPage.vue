<template>
    <message-area></message-area>
    <article class="mt-1 ui segment">
        <file-upload @action="upload" type="json"></file-upload>
        {{ filename }}
    </article>
    <article class="mt-1">
        <div v-if="getCards.length != 0">
            <div class="mr-1 ui toggle checkbox">
                <input type="checkbox" id="isSkip" v-model="isSkip" />
                <label for="isSkip">情報の更新を行わない</label>
            </div>
            <ModalButton @action="store">DBに登録する</ModalButton>
        </div>
        <form class="ui large form mt-2" v-if="$store.getters.isLoad == false">
            <div class="inline field">
                <label>エキスパンション名：</label>{{ setCode }}
            </div>
            <div class="field">
                <table class="ui table striped six column">
                    <thead>
                        <tr>
                            <th class="one wide">番号</th>
                            <th class="five wide left aligned">カード名</th>
                            <th class="four wide">英名</th>
                            <th class="">色</th>
                            <th class="">言語</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="card in getCards" :key="card.id">
                            <td class="one wide">{{ card.number }}</td>
                            <td>
                                <input type="text" v-model="card.name" />
                                <label v-if="card.promotype != ''"
                                    >≪{{ card.promotype }}≫</label
                                >
                                <foiltag :isFoil="card.isFoil"></foiltag>
                            </td>
                            <td>
                                {{ card.en_name }}
                            </td>
                            <td>
                                <label
                                    class="ui large label"
                                    :class="colorlabel(card.color)"
                                    >{{ colortext(card.color) }}</label
                                >
                            </td>
                            <td>
                                {{ card.language }}
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
        <loading
            :active="isLoading"
            :can-cancel="false"
            :is-full-page="true"
        ></loading>
    </article>
</template>
<script>
import Loading from "vue-loading-overlay";
import FileUpload from "../component/FileUpload.vue";
import MessageArea from "../component/MessageArea.vue";
import ListPagination from "../component/ListPagination.vue";
import ModalButton from "../component/ModalButton.vue";
import { AxiosTask } from "../../component/AxiosTask";
import FoilTag from "../component/FoilTag.vue";

import axios from "axios";
export default {
    data() {
        return {
            filename: "ファイルを選択してください",
            setCode: "",
            isSkip: false,
            isLoading: false,
        };
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
            this.isLoading = true;
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
                    let status = e.response.status;
                    if (status == 422) {
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
                    } else {
                        const msgs = e.response.data.detail;
                        console.log(msgs);
                        this.$store.dispatch("message/errorhtml", msgs);
                    }
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        store: async function () {
            const task = new AxiosTask(this.$store);
            const list = this.$store.getters.card;
            await Promise.all(
                list.map(async (card) => {
                    const success = function (response, store) {};
                    card["isSkip"] = this.isSkip;
                    await task.post("/database/card", card, success);
                })
            ).catch(() => {
                console.error("error");
            });
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
        Loading,
        pagination: ListPagination,
        ModalButton: ModalButton,
        foiltag: FoilTag,
    },
};
</script>
<style>
.wall {
    background-color: white;
    padding: 1em;
}
</style>
