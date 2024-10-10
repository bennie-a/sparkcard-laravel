<script>
import axios from "axios";
import Loading from "vue-loading-overlay";
import MessageArea from "./component/MessageArea.vue";
import { AxiosTask } from "../component/AxiosTask";
import ListPagination from "./component/ListPagination.vue";
import ModalButton from "./component/ModalButton.vue";
import FoilTag from "./component/FoilTag.vue";
import ImageModal from "./component/ImageModal.vue";
import SCDatePicker from "./component/SCDatePicker.vue";
import PgList from "./component/PgList.vue";
import { reactive } from "vue";

export default {
    components: {
        Loading,
        "message-area": MessageArea,
        pagination: ListPagination,
        ModalButton: ModalButton,
        foiltag: FoilTag,
        "image-modal": ImageModal,
        scdatepicker:SCDatePicker,
        pglist:PgList
    },
    data() {
        return {
            selectedSet: "",
            color: "",
            isFoil: false,
            name: "",
            arrivalDate: new Date(),
            cost: 28,
            isLoading: false,
            vendorTypeList:reactive([]),
            vendorType:1,
            vendor:'晴れる屋'
        };
    },
    async created() {
        await axios
            .get("/api/vendor")
            .then((response) => {
                this.vendorTypeList.value = response.data;
            })
            .catch((e) => {
                console.error(e);
            })
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
            this.isLoading = true;
            console.log("wisdom guild search");
            this.$store.dispatch("clearMessage");
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
                    this.isLoading = false;
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
                        market_price:this.formatPrice(c.price),
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
        handleupdate:function(value) {
            this.arrivalDate = value;
        },

        formatPrice:function(price) {
            let formattedPrice = String(price);
            return formattedPrice.indexOf(",") != -1
                                ? formattedPrice.replace(",", "")
                                : formattedPrice
        }
    }
};
</script>

<template>
    <message-area />
    <article class="mt-1 ui form segment">
        <div class="five fields">
            <div class="field">
                <label>カード名(一部)</label>
                <input v-model="name" type="text" />
            </div>

            <div class="three wide column field">
                <label for="">セット名</label>
                <div class="ui input">
                    <input
                        v-model="selectedSet"
                        type="text"
                        autocomplete="on"
                        list="setlist"
                        @input="suggestSet"
                    />
                    <datalist id="setlist">
                        <option v-for="n in suggestions"
                        :key="n">
                            {{ n.attr }}
                        </option>
                    </datalist>
                </div>
            </div>
            <div class="two wide column field">
                <label>色</label>
                <select v-model="color" class="ui dropdown">
                    <option value=""></option>
                    <option value="W">白</option>
                    <option value="U">青</option>
                    <option value="B">黒</option>
                    <option value="R">赤</option>
                    <option value="G">緑</option>
                    <option value="M">多色</option>
                    <option value="L">無色</option>
                    <option value="A">アーティファクト</option>
                    <option value="Land">土地</option>
                </select>
            </div>
            <div class="three wide column field">
                <label for="">通常版orFoil</label>
                <div class="ui toggle checkbox">
                    <input v-model="isFoil" type="checkbox" name="isFoil" />
                    <label for="isFoil">Foilのみ検索する</label>
                </div>
            </div>
            <div class="field">
                <label style="visibility: hidden">検索ボタン</label>
                <button
                    id="search"
                    class="ui button teal ml-1"
                    :class="{ disabled: selectedSet == '' && name == '' }"
                    style=""
                    @click="search"
                >
                    検索する
                </button>
            </div>
        </div>
    </article>
    <article class="mt-2">
        <h2
            v-if="$store.getters.cardsLength != 0"
            class="ui medium dividing header"
        >
            件数：{{ $store.getters.cardsLength }}件
        </h2>

        <div v-if="$store.getters.cardsLength != 0" class="mt-2 ui form">
            <div class="four fields">
                <div class="three wide column field">
                    <label for="">入荷カテゴリ</label>
                    <select v-model="vendorType" class="mr-1 ui dropdown">
                        <option v-for="t in vendorTypeList.value" :key="t.id" :value="t.id">{{t.name }}</option>
                    </select>
                </div>
                <div class="three wide column field">
                    <label for="">入荷先名</label>
                    <input type="text" :value="vendor" class="">
                </div>
                <div class="three wide column field">
                    <label>入荷日</label>
                    <scdatepicker :selectedDate="arrivalDate" @update="handleupdate"/>

                </div>
                <div class="two wide column field">
                    <label>原価</label>
                    <div class="ui middle right labeled input">
                        <input
                            v-model="cost"
                            type="number"
                            step="1"
                            min="1"
                            class="text-stock"
                        />
                        <div class="ui basic label">円</div>
                    </div>
                </div>
                <div class="three wide column field">
                    <label style="visibility: hidden">登録ボタン</label>
                    <ModalButton @action="regist"> 登録する </ModalButton>
                </div>
            </div>
        </div>
        <div class="mt-1 ui four cards">
            <div
                v-for="(card, index) in $store.getters.sliceCard"
                :key="index"
                class="card gallery"
            >
                <div class="content">
                    <foiltag :is-foil="card.isFoil" :foiltype="card.foiltype" />
                    <div class="right floated meta">#{{ card.id }}</div>
                </div>
                <div class="image">
                    <img
                        class=""
                        :src="card.image"
                        @click="$refs.modal[index].showImage(card.id)"
                    />
                    <image-modal :id="card.id" ref="modal" :url="card.image" />
                </div>
                <div class="content">
                    <div class="header">
                        {{ card.name }}
                    </div>
                    <div class="meta">
                        {{ card.exp.name }}#{{ card.number }}
                    </div>
                    <div class="description ui right floated">
                        平均価格:<span class="price"
                            >&#xa5;{{ card.price }}</span
                        >
                    </div>
                    <div>在庫：{{ card.quantity }}</div>
                </div>
                <div class="content">
                    <div class="ui form">
                        <div class="inline field radio-button">
                            <label
                                ><input
                                    v-model="card.language"
                                    type="radio"
                                    value="JP"
                                /><span>JP</span></label
                            >
                            <label
                                ><input
                                    v-model="card.language"
                                    type="radio"
                                    value="EN"
                                /><span>EN</span></label
                            >
                            <label
                                ><input
                                    v-model="card.language"
                                    type="radio"
                                    value="IT"
                                /><span>IT</span></label
                            >
                            <label
                                ><input
                                    v-model="card.language"
                                    type="radio"
                                    value="CS"
                                /><span>CS</span></label
                            >
                            <label
                                ><input
                                    v-model="card.language"
                                    type="radio"
                                    value="CT"
                                /><span>CT</span></label
                            >
                        </div>
                        <div class="two fields">
                            <div class="eight wide field">
                                <label for="">状態</label>
                                <select
                                    v-model="card.condition"
                                    class="ui fluid dropdown"
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
                                        v-model="card.stock"
                                        type="number"
                                        step="1"
                                        min="0"
                                        class="text-stock"
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
                <pagination :count="Number(12)" />
            </div>
        </div>
        <loading
         :active="isLoading"
         :can-cancel="false" :is-full-page="true" />
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
