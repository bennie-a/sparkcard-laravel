<template>
    <message-area></message-area>
    <div v-if="setName != ''">
    <label class="ui label">{{setName}}[{{setCode}}]
    </label>
    <article class="mt-1 ui grid segment">
        <div
            class="three wide column middle aligned content ui toggle checkbox"
        >
            <input type="checkbox" id="isDraftOnly" v-model="isDraftOnly" />
            <label for="isDraftOnly">通常版のみ表示</label>
        </div>
        <div class="three wide column field">
            <select v-model="color" class="ui dropdown">
                <option value="">全て</option>
                <option value="R">赤</option>
                <option value="W">白</option>
                <option value="B">黒</option>
                <option value="G">緑</option>
                <option value="U">青</option>
                <option value="M">多色</option>
                <option value="L">無色</option>
                <option value="A">アーティファクト</option>
                <option value="Land">土地</option>
            </select>
        </div>
        <div class="seven wide column">
            <file-upload @action="upload" type="json"></file-upload>
            {{ filename }}
        </div>
    </article>
    <article class="mt-1" v-if="getCards.length != 0">
        <div class="ui large form mt-2" v-if="$store.getters.isLoad == false">
            <div class="field">
                <table class="ui table striped six column">
                    <thead>
                        <tr>
                            <th class="one wide">No.</th>
                            <th class="four wide left aligned">カード名</th>
                            <th class="three wide">特別版</th>
                            <th class="three wide">英名</th>
                            <th>カード仕様</th>
                            <th class="one wide">色</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="card in getCards" :key="card.id">
                            <td class="one wide">{{ card.number }}</td>
                            <td>
                                <input type="text" v-model="card.name" />
                                {{ card.scryfallId }}
                            </td>
                            <td>
                                <promo v-model:name="card.promotype" v-model:setcode="setCode"></promo>
                            </td>
                            <td>
                                {{ card.en_name }}
                            </td>
                            <td>{{ join(card.foiltype) }}</td>
                            <td>
                                <label
                                    class="ui large label"
                                    :class="colorlabel(card.color)"
                                    >{{ colortext(card.color) }}</label
                                >
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="this.$store.getters.cardsLength != 0" class="full-width">
                        <tr>
                            <th colspan="6">
                                <pagination></pagination>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="ui centered grid">
                <div
                    class="three wide column middle aligned content ui toggle checkbox"
                >
                    <input type="checkbox" id="isSkip" v-model="isSkip" />
                    <label for="isSkip">更新をスキップ</label>
                </div>
                <div class="three wide column">
                    <ModalButton @action="store">DBに登録する</ModalButton>
                </div>
            </div>
        </div>
    </article>
</div>
    <loading
        :active="isLoading"
        :can-cancel="false"
        :is-full-page="true"
    ></loading>
</template>
<script>
import Loading from "vue-loading-overlay";
import FileUpload from "../component/FileUpload.vue";
import MessageArea from "../component/MessageArea.vue";
import ListPagination from "../component/ListPagination.vue";
import ModalButton from "../component/ModalButton.vue";
import { AxiosTask } from "../../component/AxiosTask";
import FoilTag from "../component/tag/FoilTag.vue";
import PromoDropdown from "../component/PromoDropdown.vue";
import {ref} from 'vue';

import axios from "axios";
export default {
    components: {
        "file-upload": FileUpload,
        "message-area": MessageArea,
        "loading":Loading,
        pagination: ListPagination,
        ModalButton: ModalButton,
        foiltag: FoilTag,
        promo:PromoDropdown
    },
    data() {
        return {
            filename: "ファイルを選択してください",
            setCode: ref(this.$route.params.attr),
            setName:"",
            isSkip: false,
            isLoading: false,
            isDraftOnly: false,
            color: "",
            promoItems:[],
            name:ref("通常版")
        };
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
        join: function () {
            return function (key) {
                return key.join("|");
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
    mounted: async function () {
        this.isLoading = true;
        await axios.get('/api/database/exp/' + this.setCode, {})
                            .then((response) => {
                                this.setName = response.data.name;
                            })
                            .catch((e) => {
                                console.error(e.statusCode);
                            })
                            .finally(() => {
                                this.isLoading = false;
                            });
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
            let query = "?isDraft=" + this.isDraftOnly + "&color=" + this.color+"&setcode=" + this.setCode;

            await axios
                .post("/api/upload/card" + query, file, config)
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
            this.isLoading = true;
            const task = new AxiosTask(this.$store);
            const list = this.$store.getters.card;
            await Promise.all(
                list.map(async (card) => {
                    if (card.name != "") {
                        const success = function (response, store) {};
                        card["isSkip"] = this.isSkip;
                        await task.post("/database/card", card, success);
                    }
                })
            ).catch(() => {
                console.error("error");
            });
            this.isLoading = false;
            this.$store.dispatch(
                "setSuccessMessage",
                `${list.length}件登録が完了しました。`
            );

            console.log("store finished.");
        },
    },
};
</script>
<style>
.wall {
    background-color: white;
    padding: 1em;
}
</style>
