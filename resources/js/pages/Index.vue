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
    import lang from './component/selection/Language.vue';
    import colorDropdown from "./component/selection/ColorDropdown.vue";
    import datePicker from "./component/SCDatePicker.vue";

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
const arrivalDate = ref(new Date);
const cost = ref(28);
const isLoading = ref(false);
const vendorNum = ref(1);
const vendor = ref("");
const currentList = reactive([]);
let result = reactive([]);
const resultCount = ref(0);
const itemPerPage = 12;
const page = ref(1);

const rules = {
required: value => !!value || 'Field is required',
}

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
        color: selectedColor.value,
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

// ページ数をクリックした際の内容を取得する。
const paginatedList = computed(() => {
    if (resultCount.value == 0) {
        return [];
    }
    const start = (page.value - 1) * itemPerPage;
    const end = start + itemPerPage;

    return result.value.slice(start, end);
});

// 総ページ数を取得する。
const pageCount = computed(() =>{
    return Math.ceil(resultCount.value / itemPerPage);
});

const hasResult = () => {
    return resultCount.value > 0;
}

</script>

<template>
    <message-area />
    <v-sheet rounded class="form_sheet pa-4">
        <v-row gap="15">
            <v-col cols="3">
                <v-text-field
                label="カード名(一部)"
                    v-model="name" clearable></v-text-field>
            </v-col>
            <v-col cols="2/15">
                <v-text-field
                    label="セット略称" v-model="selectedSet" clearable></v-text-field>
            </v-col>
            <v-col cols="3/13">
                <colorDropdown v-model="selectedColor"></colorDropdown>
            </v-col>
            <v-col cols="2">
                <v-btn-toggle  v-model="isFoil" border divided mandatory density="comfortable" color="teal-lighten-1">
                    <v-btn :value="false">通常版</v-btn>
                    <v-btn :value="true">Foil</v-btn>
                </v-btn-toggle>
            </v-col>
            <v-col cols="2" class="text-right">
                <v-btn @click="search" color="teal-lighten-1">検索する</v-btn>
            </v-col>
        </v-row>
    </v-sheet>
    <article class="mt-10" v-if="hasResult()">
        <h2 class="text-title-medium"
        >
           検索結果： {{ resultCount }}件
        </h2>
        <div class="mt-2">
            <v-sheet class="form_sheet pa-4">
                <v-row gap="12">
                    <v-col cols="3">
                        <vendorType v-model="vendorNum"></vendorType>
                    </v-col>
                    <v-col cols="3">
                        <v-text-field label="取引先" v-model="vendor" :disabled="isVendorDisabled" clearable></v-text-field>
                    </v-col>
                    <v-col cols="2">
                        <datePicker v-model:selectedDate="arrivalDate"  datelabel="入荷日"></datePicker>

                    </v-col>
                    <v-col cols="2">
                        <v-text-field type="number" prefix="¥" label="原価" v-model="cost"></v-text-field>
                    </v-col>
                    <v-col class="text-right">
                        <ModalButton @action="regist"> 登録する </ModalButton>
                    </v-col>
                </v-row>
            </v-sheet>
        </div>
        <section class="mt-6">
            <v-row>
                <v-col cols="3" v-for="(card, index) in paginatedList" :key="index">
                    <v-card>
                        <div class="d-flex text-label-large font-weight-regular text-start pa-2">
                            <span  class="text-grey-darken-1">#{{card.id}}</span>
                            <span class="ml-2"><foiltag :is-foil="card.foil.is_foil" :foiltype="card.foil.name" /></span>
                        </div>
                        <image-modal :id="card.id" ref="modal" :url="card.image_url" />
                        <v-card-title  class="text-title-medium text-wrap mb-0 pb-0">
                            {{ card.name }}
                        </v-card-title>
                        <v-card-subtitle class="text-wrap">
                            {{ card.exp.name }}&#91;{{ card.exp.attr }}&#93;&#35;{{ card.number }}
                        </v-card-subtitle>
                        <v-card-text class="pt-0 text-high-emphasis">
                            <p class="mt-1 mb-0">在庫：<span v-if="card.quantity > 0">{{ card.quantity }}</span><span v-else>0</span></p>
                            <p class="text-right mt-0 mb-0">平均価格：<span class="text-title-large font-weight-bold">&#xa5;{{ card.price }}</span></p>
                            <v-divider tickness="1" class="my-2"></v-divider>
                            <v-row>
                            <v-col cols="12">
                            <lang v-model="card.language"></lang>
                            </v-col>
                        </v-row>
                        <v-row  gap="10">
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
                                    label="枚数"
                                    suffix="枚"></v-text-field>
                                </v-col>
                            </v-row>
                        </v-card-text>
                        </v-card>
                </v-col>
            </v-row>
            <!--Pagination-->
            <div class="mt-6">
                <v-pagination v-model="page" :length="pageCount" rounded="circle" :total-visible="5"></v-pagination>
            </div>
        </section>
    </article>
    <loading
     :active="isLoading"
     :can-cancel="false" :is-full-page="true" />
</template>
<style scoped>

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
</style>
