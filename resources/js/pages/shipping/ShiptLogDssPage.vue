<script setup>
import shop from '../component/tag/ShopTag.vue';
import Loading from "vue-loading-overlay";
import {useRoute, useRouter} from "vue-router";
import axios from 'axios';
import { ref, onMounted } from 'vue';
import condition from "../component/tag/ConditionTag.vue";
import imagemodal from '../component/modal/ImageModal.vue';
import foiltag from '../component/tag/FoilTag.vue';
import cardlayout from '../component/CardLayout.vue';

const route = useRoute();
const router = useRouter();

const isLoading = ref(false);
const isCopied = ref(false);
const orderId = route.params.order_id;

const detail = ref({});
// 出荷情報一覧に戻る
const toList = () => {
        router.push("/shipping");
}

// 詳細情報を取得する。
const getDetail = async () => {
    isLoading.value = true;
    await axios.get("/api/shipping/"+ orderId).
        then((response) =>{
            detail.value = response.data;
        })
        .catch()
        .finally(()=> {
            isLoading.value = false;
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
        <div class="ui  grid">
            <div class="two wide column">
                <h2 class="ui medium header">販売ショップ</h2>
                <shop :orderId="orderId"/>
            </div>
            <div class="two wide column">
                <h2 class="ui medium header">発送日</h2>
                <p>{{ detail.shipping_date }}</p>
            </div>
            <div class="four wide column">
                    <h2 class="ui medium header">購入者情報</h2>
                    <address>
                        <p>{{detail.zip_code}}<br>{{ detail.address }}</p>
                        <p>{{ detail.buyer_name }}様</p>
                    </address>
                    <v-tooltip  content-class="copied-tooltip" :open-on-hover="false" v-model="isCopied" >
                        <template v-slot:activator="{ props }">
                            <v-btn v-bind="props" color="teal-lighten-1" variant="outlined" prepend-icon="mdi-clipboard" @click="copyAddress">
                                コピー
                            </v-btn>
                        </template>
                        <span>コピーしました</span>
                    </v-tooltip>
            </div>
        </div>
    </article>
    <article>
        <h2 class="text-title-medium">商品一覧</h2>
        <v-table  class="item_list mt-4 border-thin">
            <thead>
                <tr>
                    <th>在庫ID</th>
                    <th>カード情報</th>
                    <th class="text-center">状態</th>
                    <th class="text-center">枚数</th>
                    <th class="text-center">単価</th>
                    <th class="text-center">小計</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(i, index) in detail.card" :key="index">
                <td>{{ i.id }}</td>
                <td>
                    <cardlayout v-model:card="detail.card[index]" v-model:lang="detail.card[index].lang"></cardlayout>
                </td>
                <td class="text-center"><condition :name="i.condition"/></td>
                <td class="text-center">{{i.quantity}}枚</td>
                <td class="text-center">&yen;{{ i.single_price }}</td>
                <td class="text-center">&yen;{{i.subtotal_price}}</td>
            </tr>
            </tbody>
        </v-table>
        <div class="text-center mt-6">
            <v-btn variant="outlined" color="grey-darken-1" @click="toList">一覧に戻る</v-btn>
        </div>
        <loading
         :active="isLoading"
         :can-cancel="false" :is-full-page="true" />
    </article>
</template>
<style>
.copied-tooltip {
  background-color: #26A69A!important;
}
</style>
