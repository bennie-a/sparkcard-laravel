<script>
import axios from "axios";
import NowLoading from "./component/NowLoading.vue";
import CardList from "./component/CardList.vue";
import MessageArea from "./component/MessageArea.vue";
import { AxiosTask } from "../component/AxiosTask";
export default {
    data() {
        return {
            selectedSet: "",
            color: "R",
            isFoil: false,
            name: "",
        };
    },
    computed: {
        isDisabled: function () {
            let selected = this.$store.getters["csvOption/selectedList"];
            return selected.length == 0;
        },
        // セット名の候補を取得する。
        suggestions: function () {
            return this.$store.getters["expansion/suggestions"];
        },
    },
    methods: {
        suggestSet() {
            if (this.selectedSet == "") {
                return;
            }
            this.$store.dispatch("expansion/clear");
            this.options = [];
            console.log(this.selectedSet);
            const task = new AxiosTask(this.$store);
            const query = { params: { query: this.selectedSet } };
            const success = function (response, store, query) {
                store.dispatch("expansion/setSuggestions", response.data);
                console.log(response.data);
            };
            const fail = function (e, store, query) {
                console.error(e);
            };
            task.get("/database/exp", query, success, fail);
        },
        async search() {
            this.$store.dispatch("setLoad", true);
            console.log("wisdom guild search");
            this.$store.dispatch("message/clear");
            this.$store.dispatch("clearCards");
            const query = {
                params: {
                    set: this.selectedSet,
                    color: this.color,
                    isFoil: this.isFoil,
                },
            };
            await axios
                .get("/api/database/card", query)
                .then((response) => {
                    if (response.status == 204) {
                        this.$store.dispatch(
                            "message/error",
                            "検索結果がありません。"
                        );
                        return;
                    }
                    let filterd = response.data;
                    this.$store.dispatch("setCard", filterd);

                    // this.$store.dispatch("setLoad", false);
                    this.$store.dispatch(
                        "setSuccessMessage",
                        filterd.length + "件取得しました。"
                    );
                })
                .catch((e) => {
                    console.error(e);
                    this.$store.dispatch(
                        "message/error",
                        "予期せぬエラーが発生しました。"
                    );
                })
                .finally(() => {
                    this.$store.dispatch("setLoad", false);
                });
        },
        filterdCard: function (keyword) {
            console.log(keyword);
        },

        showRegist() {
            $("#regist").modal("show");
        },
        // Notionにカード情報を登録する。
        async regist() {
            this.$store.dispatch("setLoad", true);
            console.log("Notion Resist Start");
            this.$store.dispatch("message/clear");

            const card = this.$store.getters.card;
            const checkbox = this.$store.getters["csvOption/selectedList"];
            console.log(checkbox);
            const filterd = card.filter((c) => {
                return checkbox.includes(c.id);
            });

            await Promise.all(
                filterd.map(async (c) => {
                    let query = {
                        name: c.name,
                        enname: c.enname,
                        index: c.index,
                        price: c.price.replace(",", ""),
                        attr: this.selectedSet,
                        color: c.color,
                        imageUrl: c.image,
                        stock: c.stock,
                        isFoil: c.isFoil,
                    };
                    await axios
                        .post("api/notion/card", query)
                        .then((response) => {
                            if (response.status == 200) {
                                console.log(query.name + ":登録完了");
                            } else {
                                console.log(response.status);
                            }
                            this.$store.dispatch(
                                "setSuccessMessage",
                                "登録が完了しました。"
                            );
                        })
                        .catch(({ response }) => {
                            const data = response.data;
                            this.$store.dispatch("message/error", data.message);
                        });
                })
            );
            this.$store.dispatch("setLoad", false);
            $("#regist").modal("hide");
        },
        clickCallback(pageNum) {
            this.currentPage = Number(pageNum);
        },
    },
    components: {
        "now-loading": NowLoading,
        "card-list": CardList,
        "message-area": MessageArea,
    },
};
</script>

<template>
    <message-area></message-area>
    <div class="mt-1 ui form segment">
        <div class="three fields">
            <div class="field">
                <label for="">セット名</label>
                <div class="ui input">
                    <input
                        type="text"
                        autocomplete="on"
                        list="setlist"
                        v-model="selectedSet"
                        @input="suggestSet"
                    />
                    <datalist id="setlist">
                        <option v-for="n in suggestions" :key="n">
                            {{ n.attr }}
                        </option>
                    </datalist>
                </div>
            </div>
            <div class="field">
                <label>色</label>
                <select v-model="color" class="ui dropdown">
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
        </div>
        <div class="three fields">
            <!-- <div class="field">
                <label>カード名(日本語のみ)</label>
                <input type="text" v-model="name" />
            </div> -->
        </div>
        <div class="field">
            <div class="ui toggle checkbox">
                <input
                    type="checkbox"
                    name="isFoil"
                    tabindex="0"
                    v-model="isFoil"
                />
                <label>Foil</label>
            </div>
        </div>
        <button
            id="search"
            class="ui button purple ml-1"
            @click="search"
            :class="{ disabled: selectedSet == '' }"
        >
            検索する
        </button>
    </div>
    <div class="ui divider"></div>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <button
            class="ui purple button"
            @click="showRegist"
            :class="{ disabled: isDisabled }"
        >
            Notionに登録する
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
                <button class="ui primary button" @click="regist">
                    <i class="checkmark icon"></i>登録する
                </button>
            </div>
            <now-loading></now-loading>
        </div>
    </div>
    <card-list></card-list>
    <now-loading></now-loading>
</template>
