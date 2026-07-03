<script setup>
    import { ref, reactive, computed, onMounted, watch } from "vue";
    import axios from "axios";
    import Loading from "vue-loading-overlay";
    import MessageArea from "./component/msg/MessageArea.vue";;
    import ModalButton from "./component/modal/ModalButton.vue";
    import foiltag from "./component/tag/FoilTag.vue";
    import ImageModal from "./component/modal/ImageModal.vue";
    import { AxiosTask } from "../component/AxiosTask";
    import vendorType from './component/VendorType.vue';
    import lang from './component/selection/Language.vue';
    import colorDropdown from "./component/selection/ColorDropdown.vue";
    import datePicker from "./component/date/DateInput.vue";
    import ListPagination from "./component/pagination/ListPagination.vue";
    import { usePaginate } from "./component/pagination/UsePaginate";
    import {MsgStore} from "./component/msg/MsgStore";
    import RunButton from "./component/button/RunButton.vue";
    const msgStore = MsgStore();

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
    let result = ref([]);
    const resultCount = ref(0);
    const errMsgs = ref("");
    const {
        page, pageCount, paginatedList, resetPage
    } = usePaginate(result, 12);

    const rules = {
        required: value => !!value || 'Field is required',
    }

    const require = () => {
        return name.value || selectedSet.value ?  true : 'カード名かセット略称のどちらかを入力してください。'

    }

    const conditions = ['NM', 'NM-', 'EX+', 'EX', 'PLD'];

    // Vuex Storeへのアクセス（例: 仮想的なuseStore）
    import { useStore } from "vuex";
    import { set } from "lodash";
    const store = useStore();

    const isVendorDisabled = computed(() => {
    if (vendorNum.value !== 3) {
        vendor.value = "";
        return true;
    }
    return false;
    });

    const search = async () => {
        resetPage();
        msgStore.clear();
        if (name.value == "" && selectedSet.value == "") {
            msgStore.error('カード名かセット略称のどちらかを入力してください。');
            return;
        }

        isLoading.value = true;
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
                msgStore.error(data.detail);
            } finally {
                isLoading.value = false;
            }
        };

    const regist = async () => {
        msgStore.clear();
        isLoading.value = true;

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
            msgStore.success("登録が完了しました。");
            } catch ({ response }) {
                const data = response.data;
                const msg = `ステータスコード: ${response.status} ${data.message}`;
                console.error(msg);
                msgStore.error(msg);
            } finally {
                isLoading.value = false;
            }
        };

    const formatPrice = (price) => {
        const formattedPrice = String(price);
        return formattedPrice.includes(",")
            ? formattedPrice.replace(",", "")
            : formattedPrice;
        };

    const hasResult = () => {
        return resultCount.value > 0;
    }

</script>

<template>
    <v-form rounded class="form_sheet pa-4">
        <v-row gap="15">
            <v-col cols="3">
                <v-text-field
                label="カード名"
                    v-model="name" clearable hint="カード名の一部を指定"></v-text-field>
            </v-col>
            <v-col cols="2/15">
                <v-text-field
                    label="セット略称" v-model="selectedSet" clearable hint="英数字のみ"></v-text-field>
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
                <run-button text="検索する" @action="search"></run-button>
            </v-col>
        </v-row>
    </v-form>
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
                        <image-modal :url="card.image_url" :isCover="true" />
                        <v-card-title  class="text-title-medium text-wrap mb-0 pb-0">
                            {{ card.name }}
                            <div v-if="card.promotype.id != '1'" class="text-label-large">&#8810;{{card.promotype.name}}&#8811;</div>
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
                <ListPagination v-model="page" :length="pageCount"></ListPagination>
            </div>
        </section>
    </article>
    <Loading
     :active="isLoading"
     :can-cancel="false" :is-full-page="true" />
</template>
