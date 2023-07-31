<script>
import axios from "axios";
import NowLoading from "./component/NowLoading.vue";
import CardList from "./component/CardList.vue";
import MessageArea from "./component/MessageArea.vue";
import { AxiosTask } from "../component/AxiosTask";
import ListPagination from "./component/ListPagination.vue";
import ModalButton from "./component/ModalButton.vue";
import Datepicker from "@vuepic/vue-datepicker";
import { $vfm, VueFinalModal, ModalsContainer } from "vue-final-modal";

export default {
    data() {
        return {
            selectedSet: "",
            color: "",
            isFoil: false,
            name: "",
            supplier: "オリジナルパック",
            arrivalDate: new Date(),
            cost: 23,
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
                        f.language = "JP";
                    });
                    this.cost = 23;
                    this.$store.dispatch("setCard", filterd);
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

        // 入荷情報を登録する。
        async regist() {
            this.$store.dispatch("setLoad", true);
            this.$store.dispatch("message/clear");
            console.log("Arrival Start");

            const card = this.$store.getters.card;
            const filterd = card.filter((c) => {
                return c.stock != null && c.stock > 0;
            });

            await Promise.all(
                filterd.map(async (c) => {
                    let query = {
                        card_id: c.id,
                        language: c.language,
                        quantity: c.stock,
                        cost: this.cost,
                        market_price: c.price.replace(",", ""),
                        condition: c.condition,
                        attr: c.exp.attr,
                        supplier: this.supplier,
                        isFoil: c.isFoil,
                        arrival_date: this.arrivalDate,
                    };
                    await axios
                        .post("api/arrival", query)
                        .then((response) => {
                            if (response.status == 201) {
                                console.log(c.name + ":登録完了");
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
                            const msg = `${c.name}(${c.exp.attr}):${data.message}`;
                            console.error(msg);
                            this.$store.dispatch("message/error", msg);
                        });
                })
            );
            let ul = this.$store.getters["message/errorlist"];
            if (!ul) {
                this.$store.dispatch("message/errorhtml", ul);
            }
            this.$store.dispatch("setLoad", false);
        },
        showImage: function (id) {
            const selecterId = `#${id}`;
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
        "vue-final-modal": VueFinalModal,
        ModalsContainer,
    },
};
</script>

<template>
    <message-area></message-area>
    <article class="mt-1 ui form segment">
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
                <label style="visibility: hidden">検索ボタン</label>
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
    </article>
    <article class="mt-2">
        <h2
            class="ui medium dividing header"
            v-if="this.$store.getters.cardsLength != 0"
        >
            件数：{{ this.$store.getters.cardsLength }}件
        </h2>

        <div class="mt-2 ui form" v-if="this.$store.getters.cardsLength != 0">
            <div class="four fields">
                <div class="four wide column field">
                    <label for="">仕入れ先</label>
                    <select v-model="supplier" class="mr-1 ui dropdown">
                        <option>オリジナルパック</option>
                        <option>私物</option>
                        <option>棚卸し</option>
                    </select>
                </div>
                <div class="three wide column field">
                    <label>入荷日</label>
                    <datepicker
                        input-class-name="dp_custom_input"
                        v-model="arrivalDate"
                        locale="jp"
                        :enable-time-picker="false"
                        :format="dateFormat"
                    ></datepicker>
                </div>
                <div class="two wide column field">
                    <label>原価</label>
                    <div class="ui middle right labeled input">
                        <input
                            type="number"
                            step="1"
                            min="1"
                            class="text-stock"
                            v-model="this.cost"
                        />
                        <div class="ui basic label">円</div>
                    </div>
                </div>
                <div class="three wide column field">
                    <label style="visibility: hidden">登録ボタン</label>
                    <ModalButton @action="regist">登録する</ModalButton>
                </div>
            </div>
        </div>
        <div class="mt-1 ui four cards">
            <div
                class="card gallery"
                v-for="(card, index) in this.$store.getters.sliceCard"
            >
                <div class="content">
                    <label class="ui tag label" v-if="!card.isFoil">通常</label>
                    <label class="ui tag orange label" v-if="card.isFoil"
                        >Foil</label
                    >
                    <div class="right floated meta">#{{ card.id }}</div>
                </div>
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
                        <div class="inline field radio-button">
                            <label
                                ><input
                                    type="radio"
                                    value="JP"
                                    v-model="card.language"
                                /><span>JP</span></label
                            >
                            <label
                                ><input
                                    type="radio"
                                    value="EN"
                                    v-model="card.language"
                                /><span>EN</span></label
                            >
                            <label
                                ><input
                                    type="radio"
                                    value="IT"
                                    v-model="card.language"
                                /><span>IT</span></label
                            >
                            <label
                                ><input
                                    type="radio"
                                    value="CS"
                                    v-model="card.language"
                                /><span>CS</span></label
                            >
                            <label
                                ><input
                                    type="radio"
                                    value="CT"
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
                                <label>枚数</label>
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
            </div>
        </div>
        <div class="ui grid">
            <div class="four wide column row right floated">
                <pagination :count="Number(12)"></pagination>
            </div>
        </div>
        <now-loading></now-loading>
    </article>
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
