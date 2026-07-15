<script setup>
    import Loading from "vue-loading-overlay";
    import FileUpload from "../component/FileUpload.vue";
    import ListPagination from "../component/pagination/ListPagination.vue";
    import ModalButton from "../component/modal/ModalButton.vue";
    import { AxiosTask } from "../../component/AxiosTask";
    import FoilTag from "../component/tag/FoilTag.vue";
    import PromoDropdown from "../component/selection/PromoDropdown.vue";
    import ColorDropdown from "../component/selection/ColorDropdown.vue";

    import {ref} from 'vue';
    import axios from "axios";

    const isDraftOnly = ref(false);
    const color = ref("");
// // export default {
// //     components: {
// //         "file-upload": FileUpload,
// //         "loading":Loading,
// //         pagination: ListPagination,
// //         ModalButton: ModalButton,
// //         foiltag: FoilTag,
// //         promo:PromoDropdown
// //     },
// //     data() {
// //         return {
// //             filename: "ファイルを選択してください",
// //             attr: ref(this.$route.query.attr),
// //             setname:ref(this.$route.query.setname),
// //             isSkip: false,
// //             isLoading: false,
// //             isDraftOnly: false,
// //             color: "",
// //             promoItems:[],
// //             name:ref("通常版"),
// //             checkedCard:[]
// //         };
// //     },
// //     computed: {
// //         getCards: function () {
// //             return this.$store.getters.sliceCard;
// //         },
// //         colortext: function () {
// //             return function (key) {
// //                 const colors = {
// //                     W: "白",
// //                     B: "黒",
// //                     U: "青",
// //                     R: "赤",
// //                     G: "緑",
// //                     M: "多色",
// //                     L: "無色",
// //                     A: "アーティファクト",
// //                     Land: "土地",
// //                 };
// //                 return colors[key];
// //             };
// //         },
// //         join: function () {
// //             return function (key) {
// //                 return key.join("|");
// //             };
// //         },
// //         colorlabel: function () {
// //             return function (key) {
// //                 const colors = {
// //                     W: "",
// //                     B: "black",
// //                     U: "blue",
// //                     R: "red",
// //                     G: "green",
// //                     M: "orange",
// //                     L: "purple",
// //                     A: "grey",
// //                     Land: "brown",
// //                 };
// //                 return colors[key];
// //             };
// //         },
// //     },

// //     methods: {
// //         upload: async function (file) {
// //             this.isLoading = true;
// //             this.filename = file.name;
// //             const config = {
// //                 headers: {
// //                     "Content-Type": "application/json",
// //                 },
// //             };
// //             let query = "?isDraft=" + this.isDraftOnly + "&color=" + this.color+"&setcode=" + this.attr;

// //             await axios
// //                 .post("/api/upload/card" + query, file, config)
// //                 .then((response) => {
// //                     if (response.status == 201) {
// //                         let item = response.data;
// //                         this.setCode = item.setCode;
// //                         this.$store.dispatch("setCard", item.cards);
// //                         this.checkedCard = item.cards.map(c => c.number);
// //                     }
// //                 })
// //                 .catch((e) => {
// //                     let status = e.response.status;
// //                     if (status == 422) {
// //                         const errors = e.response.data.errors;
// //                         let msgs = "<ul>";
// //                         for (let key in errors) {
// //                             console.log(key);
// //                             let array = errors[key];
// //                             array.forEach((msg) => {
// //                                 msgs += `<li>${msg}</li>`;
// //                             });
// //                         }
// //                         msgs += "</ul>";
// //                         this.$store.dispatch("message/errorhtml", msgs);
// //                     } else {
// //                         const msgs = e.response.data.detail;
// //                         console.log(msgs);
// //                         this.$store.dispatch("message/errorhtml", msgs);
// //                     }
// //                 })
// //                 .finally(() => {
// //                     this.isLoading = false;
// //                 });
// //         },
// //         store: async function () {
// //             this.isLoading = true;
// //             this.$store.dispatch("message/clear");
// //             this.$store.dispatch("clearMessage");

// //             if (this.checkedCard.length == 0) {
// //                 this.isLoading = false;
// //                 this.$store.dispatch("message/error", "登録するカードを選択してください。");
// //                 return;
// //             }

// //             const task = new AxiosTask(this.$store);
// //             const list = this.$store.getters.card;
// //             await Promise.all(
// //                 list.map(async (card) => {
// //                     if (this.checkedCard.includes(card.number) == false) {
// //                         return;
// //                     }
// //                     if (card.name != "") {
// //                         const success = function (response, store) {};
// //                         card["is_skip"] = this.isSkip;
// //                         await task.post("/database/card", card, success);
// //                     }
// //                 })
// //             ).catch(() => {
// //                 console.error("error");
// //             });
// //             this.isLoading = false;
// //             this.$store.dispatch(
// //                 "setSuccessMessage",
// //                 `${this.checkedCard.length}件登録が完了しました。`
// //             );

// //             console.log("store finished.");
// //         },
// //     },
// };
</script>
<template>
    <v-form rounded class="form_sheet pa-4">
        <v-row  gap="10" class="align-center">
            <v-col cols="2" class="mr-5">
                <v-switch v-model="isDraftOnly" label="通常版のみ表示" color="teal-lighten-1" true-icon="mdi-check"
            false-icon="mdi-close"></v-switch>
            </v-col>
            <v-col cols="3" class="mr-5">
                <color-dropdown v-model="color"></color-dropdown>
            </v-col>
            <v-col cols="5">
                <file-upload @action="upload" type="json" icon="mdi-code-json"></file-upload>
            </v-col>
        </v-row>
    </v-form>
    <article class="mt-1 ui grid segment">
        <div
        class="three wide column middle aligned content ui toggle checkbox"
        >
        </div>
        <div class="three wide column field">
        </div>
        <div class="eight wide column">
        </div>
    </article>
    <!-- <article class="mt-1" v-if="getCards.length != 0">
        <div class="ui large form mt-2" v-if="$store.getters.isLoad == false">
            <div class="field">
                <table class="ui table striped six column">
                    <thead>
                        <tr>
                            <th class="one wide"></th>
                            <th class="one wide">No.</th>
                            <th class="four wide left aligned">カード名</th>
                            <th class="three wide">特別版</th>
                            <th class="three wide">英名</th>
                            <th>カード仕様</th>
                            <th class="one wide">色</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(card, index) in getCards" :key="index">
                            <td class="one wide"><input type="checkbox" :value="card.number" v-model="checkedCard" checked></td>
                            <td class="one wide">{{ card.number }}</td>
                            <td>
                                <input type="text" v-model="card.name" />
                            </td>
                            <td>
                                <promo v-model:name="card.promotype_id" v-model:setcode="setCode"></promo>
                            </td>
                            <td>
                                {{ card.en_name }}
                            </td>
                            <td></td>
                            <td>
                                <label
                                    class="ui large label"
                                    :class="colorlabel(card.color)"
                                    >{{ card.color }}</label
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
    </article> -->
    <loading
        :active="isLoading"
        :can-cancel="false"
        :is-full-page="true"
    ></loading>
</template>

<style>
.wall {
    background-color: white;
    padding: 1em;
}
</style>
