<script setup>
    import { ref, reactive, computed, onMounted } from "vue";
    import axios from "axios";
    import loading from "vue-loading-overlay";
    import MessageArea from "./component/MessageArea.vue";
    import pagination from "./component/ListPagination.vue";
    import ModalButton from "./component/ModalButton.vue";
    import foiltag from "./component/tag/FoilTag.vue";
    import ImageModal from "./component/ImageModal.vue";
    import scdatepicker from "./component/SCDatePicker.vue";
    import pglist from "./component/PgList.vue";
    import { AxiosTask } from "../component/AxiosTask";
    import vendorType from './component/VendorType.vue';
    import lang from './component/Language.vue';

// コンポーネントの登録
const components = {
        MessageArea,
        ModalButton,
        ImageModal,
        pglist,
        };

// リアクティブデータの定義
const selectedSet = ref("");
const color = ref("");
const isFoil = ref(false);
const name = ref("");
const arrivalDate = ref(new Date());
const cost = ref(28);
const isLoading = ref(false);
const vendorNum = ref(1);
const vendor = ref("");
const currentList = reactive([]);
let result = reactive([]);
let count = ref(12);
const resultCount = ref(0);

// Vuex Storeへのアクセス（例: 仮想的なuseStore）
import { useStore } from "vuex";
const store = useStore();

// 計算プロパティ
const isDisabled = computed(() => {
  const selected = store.getters["csvOption/selectedList"];
  return selected.length === 0;
});

const suggestions = computed(() => {
  return store.getters["expansion/suggestions"];
});

const isVendorDisabled = computed(() => {
  if (vendorNum.value !== 3) {
    vendor.value = "";
    return true;
  }
  return false;
});

// メソッド
const suggestSet = () => {
  if (selectedSet.value === "") return;

  store.dispatch("expansion/clear");
  const task = new AxiosTask(store);
  const query = { params: { query: selectedSet.value } };

  task.get(
    "/database/exp",
    query,
    (response) => {
      store.dispatch("expansion/setSuggestions", response.data);
      console.log(response.data);
    },
    (e) => {
      console.error(e);
    }
  );
};

const search = async () => {
    isLoading.value = true;
    store.dispatch("message/clear");
    store.dispatch("clearCards");
    store.dispatch("clearMessage");
    result.value = [];
    resultCount.value = 0;

    const query = {
        params: {
        name: name.value,
        set: selectedSet.value,
        color: color.value,
        isFoil: isFoil.value,
        },
    };

  try {
    const response = await axios.get("/api/database/card", query);
    result.value = response.data.map((f) => {
            f.language = "JP";
            return f;
            });
    resultCount.value = result.value.length;
        } catch (e) {
            let data = e.response.data;
            console.log(data);
            store.dispatch("message/error", data.detail);
        } finally {
            isLoading.value = false;
        }
    };

const regist = async () => {
    store.dispatch("setLoad", true);
    store.dispatch("message/clear");
    store.dispatch("clearMessage");
    const card = store.getters.card;
    const filtered = card.filter((c) => c.stock != null && c.stock > 0);

    try {
        await Promise.all(
        filtered.map(async (c) => {
            const query = {
            card_id: c.id,
            language: c.language,
            quantity: c.stock,
            cost: cost.value,
            vendor_type_id: vendorNum.value,
            vendor: vendor.value,
            market_price: formatPrice(c.price),
            condition: c.condition,
            attr: c.exp.attr,
            isFoil: c.isFoil,
            arrival_date: arrivalDate.value,
            };

            const response = await axios.post("api/arrival", query);
            if (response.status === 201) {
                    console.log(c.name + ": 登録完了");
            }
        })
        );
        store.dispatch("setSuccessMessage", "登録が完了しました。");
    } catch ({ response }) {
        const data = response.data;
        const msg = `ステータスコード: ${response.status} ${data.message}`;
        console.error(msg);
        store.dispatch("message/error", msg);
    } finally {
        store.dispatch("setLoad", false);
    }
    };

const formatPrice = (price) => {
    const formattedPrice = String(price);
    return formattedPrice.includes(",")
        ? formattedPrice.replace(",", "")
        : formattedPrice;
    };

const current = (data) => {
        currentList.value = data.response;
    }

const hasResult = () => {
    return resultCount.value > 0;
}

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
                <label class="hidden">検索ボタン</label>
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
            v-if="hasResult()"
            class="ui medium dividing header"
        >
            {{ resultCount }}件
        </h2>
        <div v-if="hasResult()" class="mt-2 ui form">
            <div class="four fields">
                <div class="three wide column field">
                    <label for="">入荷カテゴリ</label>
                    <vendorType v-model="vendorNum"></vendorType>
                </div>
                <div class="three wide column field">
                    <label for="">取引先</label>
                    <input type="text" v-model="vendor" :disabled="isVendorDisabled">
                </div>
                <div class="three wide column field">
                    <label>入荷日</label>
                    <scdatepicker v-model="arrivalDate"></scdatepicker>
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
                v-for="(card, index) in currentList.value"
                :key="index"
                class="card gallery"
            >
                <div class="content">
                    <foiltag :is-foil="card.foil.is_foil" :foiltype="card.foil.name" />
                    <div class="right floated meta">#{{ card.id }}</div>
                </div>
                <div class="image">
                    <img
                        class=""
                        :src="card.image_url"
                        @click="$refs.modal[index].showImage(card.id)"
                    />
                    <image-modal :id="card.id" ref="modal" :url="card.image_url" />
                </div>
                <div class="content">
                    <div class="header">
                        {{ card.name }}
                    </div>
                    <div class="meta">
                        {{ card.exp.name }}&#35;{{ card.number }}
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
                        <lang v-model="card.language"></lang>
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
        <div  v-show="hasResult()" class="ui centered grid mt-2 mb-1">
            <pglist ref="pglistRef" v-model:list="result.value" @loadPage="current" v-model:perPage="count"></pglist>
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
