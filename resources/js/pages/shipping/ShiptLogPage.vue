<script setup>
import shop from "../component/tag/ShopTag.vue";
import scdatepicker from "../component/date/DateInput.vue";
import { useRouter } from "vue-router";
import { ref, onMounted } from "vue";
import axios from 'axios';
import Loading from "vue-loading-overlay";
import ListPagination from "@/pages/component/pagination/ListPagination.vue";
import { usePagenate } from "@/pages/component/pagination/UsePaginate";
import {MsgStore} from "@/pages/component/msg/MsgStore";

const router = useRouter();

const buyer = ref("");
const result = ref([]);
const isLoading = ref(false);
const today = new Date();
const shippingStartDate = ref(new Date());
const resultCount = ref(0);
const msgStore = MsgStore();

const {
    page, pageCount, paginatedList, resetPage
} = usePagenate(result, 10);

const fetch =  async () => {
    resetPage();
    msgStore.clear();
    isLoading.value = true;
    result.value = [];
    resultCount.value = 0;
    const query = {
                params: {
                    "buyer_name": buyer.value,
                    "shipping_date": toDateString(shippingStartDate.value),
                },
            };
   await axios.get('/api/shipping/', query)
                            .then((response) => {
                                result.value = response.data;
                                resultCount.value = result.value.length;
                            })
                            .catch((e) => {
                                const detail = e.response.data.detail;
                                console.log(e.response.data);
                                msgStore.error(detail);
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
            <v-form class="rounded form_sheet pa-4">
                <v-row gap="15">
                    <v-col cols="3">
                        <v-text-field v-model="buyer"  label="購入者名" clearable></v-text-field>
                    </v-col>
                    <v-col cols="3">
                        <scdatepicker v-model:selectedDate="shippingStartDate" datelabel="発送日"></scdatepicker>
                    </v-col>
                    <v-col cols="2" class="text-right">
                        <v-btn
                            id="search" color="teal-lighten-1" @click="fetch">
                            検索する
                            </v-btn>
                    </v-col>
                </v-row>
            </v-form>
    <article class="mt-10" v-show="resultCount > 0">
        <h2  class="text-title-medium">
            件数：{{resultCount}}件
        </h2>
        <v-table class="mt-4 border-thin">
            <thead>
                <tr class="bg-grey-lighten-3 text-bold">
                    <th width="13%" class="text-center">発送日</th>
                    <th width="15%">プラットフォーム</th>
                    <th>購入者情報</th>
                    <th width="10%" class="text-center">合計金額</th>
                    <th width="10%" class="text-center">商品数</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(r, index) in paginatedList" :key="index">
                    <td class="text-center">{{ r.shipping_date }}</td>
                    <td>
                        <shop :orderId="r.order_id"/>
                    </td>
                    <td>
                        <h3 class="mb-0 mt-0 text-title-medium">{{ r.name }}様
                        </h3>
                        <span class="text-medium-emphasis">〒{{ r.zip_code }} {{ r.address }}</span>
                    </td>
                    <td class="text-center">&yen;{{ r.total_price }}</td>
                    <td class="text-center">{{r.item_count}}点</td>
                    <td class="text-right">
                        <v-btn icon="mdi-chevron-double-right" color="teal-lighten-1" variant="text" @click="toDssPage(r.order_id)"></v-btn>
                    </td>
                </tr>
            </tbody>
            <tfoot class="full-width">
                <tr>
                    <th colspan="10">
                        <div class="right aligned">
                            <ListPagination v-model="page" :length="pageCount"/>
                        </div>
                    </th>
                </tr>
            </tfoot>
        </v-table>
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
