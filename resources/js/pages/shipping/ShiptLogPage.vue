<script setup>
import shop from "../component/ShopTag.vue";
import scdatepicker from "../component/ScDatePicker.vue";
import { useRouter } from "vue-router";
import { ref, onMounted } from "vue";
import axios from 'axios';
import Loading from "vue-loading-overlay";

const shippingDate = ref(new Date());
const orderId = "order_h4eKcB8RCjyejCthfphH2K";
const orderId2 = "C197D909A9CFF5C4";
const router = useRouter();

const result = ref([]);
const isLoading = ref(false);

const fetch =  async () => {
    isLoading.value = true;
   await axios.get('/api/shipping/')
                            .then((response) => {
                                result.value = response.data;
                            })
                            .catch()
                            .finally(() => {
                                isLoading.value = false;
                            });    
};

// 詳細画面を表示する。
const toDssPage = (orderId) => {
    router.push({
        name: "ShiptLogDss",
        params: { order_id: orderId},
    });
}

// SCDatePickerで選択した日付を設定する。
const handleupdate = (date) => {
    shippingDate.value = date;
}

onMounted(async() => {
    await fetch();
});
</script>
<template>
        <article class="mt-1 ui form segment">
        <div class="two fields">
            <div class="four wide field">
                <label>購入者名</label>
                <input v-model="buyer" type="text">
            </div>
            <div class="three wide field">
                <label for="">発送日</label>
                <div>
                    <scdatepicker :selectedDate="shippingDate" @update="handleupdate"/>
                </div>
            </div>
        </div>
        <button
            id="search"
            :class="{ disabled: cardname == '' && setname == '' }"
                class="ui button teal"
                @click="search"
            >
            検索
            </button>
    </article>
    <article class="mt-2">
        <h2 class="ui medium dividing header">
            件数：1件
        </h2>
        <table class="ui striped table">
            <thead>
                <tr>
                    <th class="one wide">注文ID</th>
                    <th class="two wide center aligned">販売ショップ</th>
                    <th class="five wide">購入者名</th>
                    <th class="">合計金額</th>
                    <th class="">発送日</th>
                    <th class="one wide">商品数</th>
                    <th class="one wide"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(r, index) in result" :key="index">
                    <td>{{r.order_id}}</td>
                    <td class=" center aligned"><shop :orderId="r.order_id"/></td>
                    <td><h3 class="ui header">{{ r.name }}
                        <span class="sub header">〒{{ r.zip_code }} {{ r.address }}</span>
                    </h3>
                    </td>
                    <td><i class="bi bi-currency-yen"></i>{{ r.total_price }}</td>
                    <td>{{ r.shipping_date }}</td>
                    <td class="one wide center aligned">{{r.item_count}}点</td>
                    <td class="center aligned selectable">
                        <a @click="toDssPage(r.order_id)"><i class="bi bi-chevron-double-right"></i></a>
                    </td>
                </tr>
            </tbody>
            <tfoot class="full-width">
                <tr>
                    <th colspan="10">
                        <div class="right aligned">
                            <pagination/>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </table>
    </article>
    <loading
         :active="isLoading"
         :can-cancel="false" :is-full-page="true" />

</template>
<style>
</style>