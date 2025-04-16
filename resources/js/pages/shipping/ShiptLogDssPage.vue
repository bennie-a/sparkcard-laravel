<script setup>
import shop from '../component/tag/ShopTag.vue';
import Loading from "vue-loading-overlay";
import {useRoute, useRouter} from "vue-router";
import axios from 'axios';
import { ref, onMounted } from 'vue';
import condition from "../component/tag/ConditionTag.vue";
import imagemodal from '../component/ImageModal.vue';
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
    let copyText = `${detail.value.zipcode}\n${detail.value.address}\n${detail.value.buyer_name}様`;
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
            <div class="four wide column">
                <h2 class="ui medium header">注文番号</h2>
                <p>{{ detail.order_id }}</p>
            </div>
            <div class="two wide column">
                <h2 class="ui medium header">発送日</h2>
                <p>{{ detail.shipping_date }}</p>
            </div>
            <div class="four wide column">
                    <h2 class="ui medium header">購入者情報</h2>
                    <address>
                        <p>{{detail.zipcode}}<br>{{ detail.address }}</p>
                        <p>{{ detail.buyer_name }}様</p>
                    </address>
                    <button class="ui teal basic tiny button" id="copy" @click="copyAddress">
                        <i class="bi bi-clipboard-fill mr-half"></i>コピー
                    </button>
                    <div v-if="isCopied" class="ui left pointing teal label">コピーしました</div>
            </div>
        </div>
        <h2 class="ui medium header">商品一覧</h2>
        <table class="ui table stripe">
            <thead>
                <tr>
                    <th class="two wide">在庫ID</th>
                    <th>カード</th>
                    <th class="center aligned">状態</th>
                    <th class="center aligned">枚数</th>
                    <th class="center aligned">単価</th>
                    <th class="center aligned">小計</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(i, index) in detail.items" :key="index">
                <td>{{ i.id }}</td>
                <td>
                    <cardlayout v-model="detail.items[index]"></cardlayout>
                </td>
                <td class="center aligned"><condition :name="i.condition"/></td>
                <td class="center aligned">{{i.quantity}}枚</td>
                <td class="center aligned"><i class="bi bi-currency-yen"></i>{{ i.single_price }}</td>
                <td class="center aligned"><i class="bi bi-currency-yen"></i>{{i.subtotal_price}}</td>
            </tr>
            </tbody>
        </table>
        <div class="text-center">
            <button class="ui gray basic button" @click="toList">一覧に戻る</button>
        </div>
        <loading
         :active="isLoading"
         :can-cancel="false" :is-full-page="true" />
    </article>
</template>
<style>
address {
    font-style: normal;
}
address > p {
    margin-bottom: 0.5em!important;
}

#copy:hover {
    color: white!important;
    background: #00B2AA!important;
    border: 0!important;
}
</style>