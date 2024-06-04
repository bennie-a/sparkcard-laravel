<script setup>
import shop from "../component/ShopTag.vue";
import scdatepicker from "../component/ScDatePicker.vue";
import { useRouter } from "vue-router";
import { ref } from "vue";
const shippingDate = ref(new Date());
const orderId = "order_GCBU9evkzEQ9Bnae7n4qnG";
const router = useRouter();

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
                    <th class="one wide">出荷ID</th>
                    <th class="two wide center aligned">販売ショップ</th>
                    <th class="seven wide">購入者名</th>
                    <th class="">合計金額</th>
                    <th class="two wide">発送日</th>
                    <th class="one wide">商品数</th>
                    <th class="one wide"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1111</td>
                    <td class=" center aligned"><shop :orderId="orderId"/></td>
                    <td><h3 class="ui header">三崎健太<span class="sub header">〒490-1400 愛知県海部郡飛島村大字飛島新田字竹之郷ヨタレ南ノ割979-3</span></h3></td>
                    <td><i class="bi bi-currency-yen"></i>333</td>
                    <td>2024/02/04</td>
                    <td class="one wide center aligned">1点</td>
                    <td class="center aligned selectable">
                        <a @click="toDssPage(orderId)"><i class="bi bi-chevron-double-right"></i></a>
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
</template>
<style>
</style>