<script setup>
import shop from "../component/ShopTag.vue";
import scdatepicker from "../component/SCDatePicker.vue";
import { useRouter } from "vue-router";
import { ref, onMounted, reactive } from "vue";
import axios from 'axios';
import Loading from "vue-loading-overlay";
import pglist from "../component/PgList.vue";

const shippingDate = ref(new Date());
const router = useRouter();
const buyer = ref("");
let result = reactive([]);
const isLoading = ref(false);
const today = new Date();
let shippingStartDate = new Date();
const currentList = reactive([]);
const resultCount = ref(0);

const pglistRef = ref();

const fetch =  async () => {
    isLoading.value = true;
    const query = {
                params: {
                    "buyer_name": buyer.value,
                    "shipping_date": toDateString(shippingStartDate),
                },
            };
   await axios.get('/api/shipping/', query)
                            .then((response) => {
                                result.value = response.data;
                                resultCount.value = result.value.length;
                            })
                            .catch((e) => {
                                console.error(e);
                                isLoading.value = false;
                            })
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
const handleStartDate = (date) => {
    shippingStartDate = date;
}

// const handleEndDate = (date) => {
//     shippingEndDate.value = date;
// }

onMounted(async() => {
    await fetch();
});

// 発送日が今日かどうか判定する。
const isToday = (date) => {
    return today === date;
};

// pglistから送られた1ページあたりの結果を取得する。
const current = (data) => {
    currentList.value = data.response;
}

const toDateString = (date) => {
    if (date != null) {
        return date.toLocaleDateString("ja-JP", {year:"numeric", month:"2-digit",day:"2-digit" });
    }
    return null;
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
                <label for="">発送日(開始)</label>
                <div>
                    <scdatepicker :selectedDate="shippingStartDate" @update="handleStartDate"/>
                </div>
            </div>
            {{ buyer }}
            <!-- <div class="three wide field">
                <label for="">発送日(終了)</label>
                <div>
                    <scdatepicker :selectedDate="shippingEndDate" @update="handleEndDate"/>
                </div>
            </div> -->
        </div>
        <button
            id="search"
                class="ui button teal"
                @click="fetch"
            >
            検索
            </button>
    </article>
    <article class="mt-2" v-show="resultCount != 0">
        <h2 class="ui medium dividing header">
            件数：{{resultCount}}件
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
                <tr v-for="(r, index) in currentList.value" :key="index">
                    <td>{{r.order_id}}</td>
                    <td class=" center aligned">
                        <shop :orderId="r.order_id"/>
                    </td>
                    <td><h3 class="ui header">{{ r.name }}
                        <span class="sub header">〒{{ r.zip_code }} {{ r.address }}</span>
                    </h3>
                    </td>
                    <td><i class="bi bi-currency-yen"></i>{{ r.total_price }}</td>
                    <td :class="[isToday(r.shipping_date) ? 'positive': '', isToday(r.shipping_date)?'tobold':'']">{{ r.shipping_date }}</td>
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
                            <pglist ref="pglistRef" v-model:list="result.value" @loadPage="current"></pglist>
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
<style scoped>
.tobold {
    font-weight: bold;
}
</style>