<script>
import axios from "axios";
import NowLoading from "./component/NowLoading.vue";
import CardList from "./component/CardList.vue";
import MessageArea from "./component/MessageArea.vue";
import { AxiosTask } from "../component/AxiosTask";
import ListPagination from "./component/ListPagination.vue";
import ModalButton from "./component/ModalButton.vue";
import Datepicker from "@vuepic/vue-datepicker";
export default {
    data() {
        return {
            selectedSet: "",
            color: "",
            isFoil: false,
            name: "",
            supplier: "オリジナルパック",
            arrivalDate: new Date(),
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
                    name: this.name,
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
                    filterd.map((f) => {
                        f.language = "日本語";
                    });
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

        // Notionにカード情報を登録する。
        async regist() {
            this.$store.dispatch("setLoad", true);
            console.log("Arrival Start");
            this.$store.dispatch("message/clear");

            const card = this.$store.getters.card;
            const filterd = card.filter((c) => {
                return c.stock != null && c.stock > 0;
            });

            await Promise.all(
                filterd.map(async (c) => {
                    let query = {
                        id: c.id,
                        name: c.name,
                        enname: c.enname,
                        index: c.index,
                        price: c.price.replace(",", ""),
                        attr: c.exp.attr,
                        color: c.color,
                        imageUrl: c.image,
                        stock: c.stock,
                        isFoil: c.isFoil,
                        language: c.language,
                        condition: c.condition,
                    };
                    await axios
                        .post("api/arrival", query)
                        .then((response) => {
                            if (response.status == 201) {
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
                            $msg = `${query.name}(${query.attr}):${data.message}`;
                            this.$store.commit("message/addErrorList", $msg);
                        });
                })
            );
            let ul = this.$store.getters["message/errorlist"];
            if (!ul) {
                this.$store.dispatch("message/errorhtml", ul);
            }
            $("#regist").modal("hide");
            this.$store.dispatch("setLoad", false);
        },
        showImage: function (id) {
            const selecterId = `#${id}`;
            console.log(selecterId);
            $(selecterId).modal("show");
        },
        dateFormat: function (date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}/${month}/${day}`;
        },
    },
    components: {
        "now-loading": NowLoading,
        "card-list": CardList,
        "message-area": MessageArea,
        pagination: ListPagination,
        ModalButton: ModalButton,
        datepicker: Datepicker,
    },
};
</script>

<template>
    <message-area></message-area>
    <div class="mt-1 ui form segment">
        <div class="five fields">
            <div class="field">
                <label>カード名(一部)</label>
                <input type="text" v-model="name" />
            </div>

            <div class="three wide column field">
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
            <div class="two wide column field">
                <label>色</label>
                <select v-model="color" class="ui dropdown">
                    <option value=""></option>
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
            <div class="three wide column field">
                <label for="">通常版orFoil</label>
                <div class="ui toggle checkbox">
                    <input type="checkbox" name="isFoil" v-model="isFoil" />
                    <label for="isFoil">Foilのみ検索する</label>
                </div>
            </div>
            <div class="field">
                <label for="" style="visibility: hidden">検索ボタン</label>
                <button
                    id="search"
                    class="ui button teal ml-1"
                    @click="search"
                    :class="{ disabled: selectedSet == '' && name == '' }"
                    style=""
                >
                    検索する
                </button>
            </div>
        </div>
    </div>
    <div class="mt-2" v-if="this.$store.getters.cardsLength != 0">
        <h5 class="ui sub header">仕入れ先</h5>
        <select v-model="supplier" class="mr-1 ui dropdown">
            <option>オリジナルパック</option>
            <option>棚卸し</option>
            <option>私物</option>
        </select>
    </div>
    <div>
        <datepicker
            placeholder="ここをクリックして日時を入力"
            v-model="arrivalDate"
            locale="jp"
            :enable-time-picker="false"
            :format="dateFormat"
        ></datepicker>
    </div>
    <div class="mt-1 ui four cards">
        <div class="card gallery" v-for="card in this.$store.getters.sliceCard">
            <div class="content meta">ID:{{ card.id }}</div>
            <div class="image">
                <img
                    class=""
                    v-bind:src="card.image"
                    @click="showImage(card.id)"
                />
                <div class="ui tiny modal" v-bind:id="card.id">
                    <i class="close icon"></i>
                    <div class="image content">
                        <img v-bind:src="card.image" class="image" />
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="header">{{ card.name }}</div>
                <div class="meta">{{ card.exp.name }}</div>
                <div class="description ui right floated">
                    平均価格:<span class="price">&yen{{ card.price }}</span>
                </div>
            </div>
            <div class="content">
                <div class="ui form">
                    <!-- {{ card.language }} -->
                    <div class="inline field radio-button">
                        <label
                            ><input
                                type="radio"
                                value="日本語"
                                v-model="card.language"
                            /><span>JP</span></label
                        >
                        <label
                            ><input
                                type="radio"
                                value="英語"
                                v-model="card.language"
                            /><span>EN</span></label
                        >
                        <label
                            ><input
                                type="radio"
                                value="イタリア語"
                                v-model="card.language"
                            /><span>IT</span></label
                        >
                        <label
                            ><input
                                type="radio"
                                value="簡体中国語"
                                v-model="card.language"
                            /><span>CS</span></label
                        >
                        <label
                            ><input
                                type="radio"
                                value="繫体中国語"
                                v-model="card.language"
                            /><span>CT</span></label
                        >
                    </div>
                    <div class="two fields">
                        <div class="eight wide field">
                            <label for="">状態</label>
                            <select
                                class="ui fluid dropdown"
                                v-model="card.condition"
                            >
                                <option value="NM">NM</option>
                                <option value="NM-">NM-</option>
                                <option value="EX+">EX+</option>
                                <option value="EX">EX</option>
                                <option value="PLD">PLD</option>
                            </select>
                        </div>
                        <div class="eight wide field">
                            <label for="">枚数</label>
                            <div class="ui middle right labeled input">
                                <input
                                    type="number"
                                    step="1"
                                    min="0"
                                    class="text-stock"
                                    v-model="card.stock"
                                />
                                <div class="ui basic label">枚</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="extra content">
                <ModalButton @action="regist">
                    <i class="add icon"></i>入荷する</ModalButton
                >
            </div>
        </div>
    </div>
    <div class="ui grid">
        <div class="four wide column row right floated">
            <pagination :count="Number(12)"></pagination>
        </div>
    </div>
    <now-loading></now-loading>
</template>
<style scoped>
div.card > .image {
    height: 120px !important;
    overflow: hidden;
    /* height: min-content; */
}
div.image img {
    width: fit-content;
    height: 100% !important;
    object-position: 50% 20%;
    object-fit: cover;
    cursor: pointer;
}

div.gallery div.header {
    font-size: 1rem !important;
    padding-top: 0.5rem;
    padding-bottom: 0rem;
}
div.gallery span.price {
    font-weight: 700;
    font-size: 1.3rem;
}

input.text-stock {
    width: 6vw;
}

.radio-button > :first-child,
.radio-button > label span {
    margin-right: 0.5rem !important;

    cursor: pointer;
}

.ui.form .inline.field > :first-child {
    margin-right: 0 !important;
}
.radio-button {
    line-height: 3;
}
.radio-button > label input {
    display: none; /* デフォルトのinputは非表示にする */
}
.radio-button > label span {
    padding: 5px 10px !important; /* 上下左右に余白をトル */
    border-radius: 5px;
    color: var(--teal);
    border: 1px solid var(--teal);
}

label input:checked + span {
    color: #fff; /* 文字色を白に */
    background: var(--teal); /* 背景色を薄い赤に */
    border: 0;
}
</style>
