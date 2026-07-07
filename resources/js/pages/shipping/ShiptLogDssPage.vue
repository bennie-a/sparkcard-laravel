<script setup>
import shop from '../component/tag/ShopTag.vue';
import Loading from "vue-loading-overlay";
import {useRoute, useRouter} from "vue-router";
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import condition from "../component/tag/ConditionTag.vue";
import imagemodal from '../component/modal/ImageModal.vue';
import foiltag from '../component/tag/FoilTag.vue';
import cardlayout from '../component/CardLayout.vue';
import { usePaginate } from "@/pages/component/pagination/UsePaginate";
import PaginatedTable from '../component/pagination/PaginatedTable.vue';
import {LoadStore} from "@/stores/loading/LoadStore.js";

const loadStore = LoadStore();
const route = useRoute();
const router = useRouter();

const isCopied = ref(false);
const orderId = route.params.order_id;

const detail = ref({});
const cardList = computed(() => detail.value.card ?? []);

// 出荷情報一覧に戻る
const toList = () => {
        router.push("/shipping");
}

const {
    page, pageCount, paginatedList, resetPage
} = usePaginate(cardList, 10);

// 詳細情報を取得する。
const getDetail = async () => {
    loadStore.on();
    await axios.get("/api/shipping/"+ orderId).
    then((response) =>{
        detail.value = response.data;
        resetPage();
        })
        .catch()
        .finally(()=> {
            loadStore.off();
        });
}

const copyAddress = () => {
    isCopied.value = true;
    let copyText = `${detail.value.zip_code}\n${detail.value.address}\n${detail.value.buyer_name}様`;
    navigator.clipboard.writeText(copyText);
    setTimeout(() => {
        isCopied.value = false;
    }, 2000);
}
onMounted(async() => {
    await getDetail();
});
</script>

<template>
    <article>
        <v-row gap="25">
            <v-col cols="4">
                <v-card variant="flat" color="#f5f5f5" class="mx-auto pb-2">
                    <v-card-text>
                        <address>
                            <p>{{detail.zip_code}}</p>
                            <p>{{ detail.address }}</p>
                            <p class="text-title-large">{{ detail.buyer_name }}様</p>
                        </address>
                    </v-card-text>
                    <v-card-actions class="pl-4">
                        <v-tooltip  content-class="copied-tooltip" :open-on-hover="false" v-model="isCopied" >
                            <template v-slot:activator="{ props }">
                                <v-btn size="small" v-bind="props" color="teal-lighten-1" variant="outlined" prepend-icon="mdi-clipboard" @click="copyAddress">
                                        コピー
                                </v-btn>
                            </template>
                            <span>コピーしました</span>
                        </v-tooltip>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="4">
                <dl class="mt-0 mb-0">
                    <div class="mb-4">
                        <dt class="text-title-medium">販売ショップ</dt>
                        <dd class="mt-1 ml-0">
                            <shop :orderId="orderId"/>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-title-medium">発送日</dt>
                        <dd class="mt-1 ml-0">{{ detail.shipping_date }}</dd>
                    </div>
                </dl>
            </v-col>
        </v-row>
        <div class="ui  grid">
        </div>
    </article>
    <article class="mt-8">
        <h2 class="text-title-large">商品一覧</h2>
        <PaginatedTable v-model="page" :items="paginatedList" :page-count="pageCount" :hit-count="cardList.length">
            <template #header>
                    <th>在庫ID</th>
                    <th>カード情報</th>
                    <th class="text-center">状態</th>
                    <th class="text-center">枚数</th>
                    <th class="text-center">単価</th>
                    <th class="text-center">小計</th>
            </template>
            <template #row="{ item }">
                <td>{{ item.id }}</td>
                <td>
                    <cardlayout :card="item" :lang="item.lang"></cardlayout>
                </td>
                <td class="text-center"><condition :name="item.condition"/></td>
                <td class="text-center">{{item.quantity}}枚</td>
                <td class="text-center">&yen;{{ item.single_price }}</td>
                <td class="text-center">&yen;{{item.subtotal_price}}</td>
            </template>
        </PaginatedTable>
        <div class="text-center mt-6">
            <v-btn variant="outlined" color="teal-lighten-1" @click="toList"><v-icon icon="mdi-chevron-double-left" start></v-icon>一覧に戻る</v-btn>
        </div>
    </article>
</template>
<style>
.copied-tooltip {
  background-color: #26A69A!important;
}


</style>
