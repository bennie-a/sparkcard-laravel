<script setup>
    import { ref, reactive, computed, onMounted } from "vue";
    import axios from "axios";
    import loading from "vue-loading-overlay";
    import MessageArea from "./component/MessageArea.vue";;
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
const selectedColor = ref("");
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

const rules = {
required: value => !!value || 'Field is required',
}

const colorItems = [
    {state:'白', icon:'mdi-weather-sunny', item_value:'W', color:'yellow-lighten-1'},
    {state:'青', icon:'mdi-water', item_value:'U', color:'blue-lighten-1'},
    {state:'黒', icon:'mdi-skull', item_value:'B', color:'grey-darken-3'},
    {state:'赤', icon:'mdi-fire', item_value:'R', color:'red-lighten-1'},
    {state:'緑', icon:'mdi-pine-tree-variant', item_value:'G', color:'green-lighten-1'},
    {state:'多色', icon:'mdi-multiplication-box', item_value:'M', color:'orange-lighten-1'},
    {state:'無色', icon:'mdi-invert-colors-off', item_value:'L', color:'purple-lighten-1'},
    {state:'アーティファクト', icon:'mdi-key', item_value:'A', color:'blue-grey-lighten-1'},
    {state:'土地', icon:'mdi-land-plots', item_value:'Land', color:'brown-lighten-1'},
    {state:'アートカード', icon:'mdi-palette', item_value:'Art', color:'pink-lighten-1'},
];

const conditions = ['NM', 'NM-', 'EX+', 'EX', 'PLD'];

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
        selectedColor: selectedColor.value,
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
    const card = result.value;
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
    <v-sheet color="grey-lighten-4" rounded class="pa-4">
        <v-row class="">
            <v-col cols="3">
                <v-text-field
                label="カード名(一部)"
                    v-model="name" clearable></v-text-field>
            </v-col>
            <v-col cols="2">
                <v-text-field
                    label="セット略称" v-model="selectedSet" clearable></v-text-field>
            </v-col>
            <!-- <v-col cols="5">
                <v-chip-group v-model="selectedColor" column selected-class="">
                    <v-chip v-for="color in colorItems" :key="color.item_value" :value="color.item_value"
                     class="ma-1 bg-white" :variant="selectedColor === color.item_value ? 'flat' : 'outlined'"
                     :color="color.color">
                        <div class="d-flex align-center ga-1">
                            <v-icon :icon="color.icon" size="18"/>{{ color.item_value }}
                        </div>
                    </v-chip>
                </v-chip-group>
            </v-col> -->
            <v-col cols="3">
                <v-select
                    label="色"
                    v-model="selectedColor"
                    :items="colorItems"
                    item-title="state"
                    item-value="item_value"
                    variant="outlined"
                    density="compact"
                    bg-color="white"
                    clearable
                >
            </v-select>
            </v-col>
            <v-col cols="2">
                <v-btn-toggle  v-model="isFoil" border divided mandatory density="comfortable">
                    <v-btn :value="false">通常版</v-btn>
                    <v-btn :value="true">Foil</v-btn>
                </v-btn-toggle>
            </v-col>
            <v-col cols="2" class="text-right">
                <v-btn @click="search">検索する</v-btn>
            </v-col>
        </v-row>
    </v-sheet>
    <article class="mt-10">
        <h2
            v-if="hasResult()"
            class="text-title-medium"
        >
           検索結果： {{ resultCount }}件
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
        <section>
            <v-row>
                <v-col cols="3" v-for="(card, index) in currentList.value" :key="index">
                    <v-card>
                        <div class="d-flex text-label-large font-weight-regular text-start pa-2">
                            <span  class="text-grey-darken-1">#{{card.id}}</span>
                            <span class="ml-2"><foiltag :is-foil="card.foil.is_foil" :foiltype="card.foil.name" /></span>
                        </div>
                        <v-img :src="card.image_url"  cover height="140" class="image-position"></v-img>
                        <v-card-title  class="text-title-medium text-wrap mb-0 pb-0">
                            {{ card.name }}
                        </v-card-title>
                        <v-card-subtitle class="text-wrap">
                            {{ card.exp.name }}&#91;{{ card.exp.attr }}&#93;&#35;{{ card.number }}
                        </v-card-subtitle>
                        <v-card-text class="pt-0 text-high-emphasis text-right">
                            <p class="mt-1 mb-0">平均価格：<span class="text-title-large font-weight-bold">&#xa5;{{ card.price }}</span></p>
                            <p class="mt-0">在庫：<span v-if="card.quantity > 0">{{ card.quantity }}</span><span v-else>0</span></p>
                            <lang v-model="card.language"></lang>
                        <v-row>
                            <v-col cols="12">
                            </v-col>
                        </v-row>
                        <v-row class="text-center" gap="10">
                            <v-col>
                                <v-select
                                    v-model="card.condition"
                                    variant="outlined"
                                    :items="conditions"
                                    density="compact"
                                    label="状態"></v-select>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    v-model="card.stock"
                                    type="number"
                                    step="1"
                                    min="0"
                                    suffix="枚"></v-text-field>
                                </v-col>
                            </v-row>
                        </v-card-text>
                        </v-card>
                </v-col>
            </v-row>
        </section>
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
                        <div v-if="card.promotype.id != '1'">&#8810;{{card.promotype.name}}&#8811;</div>
                    </div>
                    <div class="meta">
                        {{ card.exp.name }}&#91;{{ card.exp.attr }}&#93;&#35;{{ card.number }}
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
.image-position:deep(img) {
  object-position: center -40px; /* 右側を基準に表示 */
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
