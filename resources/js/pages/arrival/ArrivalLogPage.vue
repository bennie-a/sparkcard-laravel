<script setup>
import { onMounted, ref, shallowRef } from 'vue';
import { useRouter } from "vue-router";
import vendortag from "../component/tag/VendorTag.vue"
import Loading from "vue-loading-overlay";
import axios from 'axios';
import UseDateFormatter from '../../functions/UseDateFormatter.js';
import DateRangeInput from '../component/date/DateRangeInput.vue';

import {groupConditionStore} from "@/stores/arrival/GroupCondition";
import {arrDateConditionStore} from "@/stores/arrival/arrDateCondition";

import { storeToRefs } from 'pinia';

import RunButton from '../component/button/RunButton.vue';
import CardLayout from '../component/CardLayout.vue';
import { usePagenate } from '../component/pagination/UsePaginate';
import ListPagination from '../component/pagination/ListPagination.vue';
import LinkIconButton from '../component/button/LinkIconButton.vue';

const gcStore = groupConditionStore();
const arrDateStore = arrDateConditionStore();

// 検索条件
const {startDate, endDate, itemname} = storeToRefs(gcStore);

const selectedDate = ref([]);

// 検索結果
const result = ref([]);
const resultCount = ref(0);
const isLoading = ref(false);

const router = useRouter();
const {toString} = UseDateFormatter();

const {
    page, pageCount, paginatedList, resetPage
} = usePagenate(result, 10);

// 入荷情報検索
const fetch =  async () => {
    isLoading.value = true;
    result.value = [];
    resultCount.value = 0;
    resetPage();
    const query = {
                params: {
                    "card_name": itemname.value,
                    "start_date": toString(startDate.value),
                    "end_date" : toString(endDate.value)
                },
            };
   await axios.get('/api/arrival/grouping', query)
                            .then((response) => {
                                result.value = response.data;
                                resultCount.value = result.value.length;
                            })
                            .catch((e) => {
                                let data = e.response.data;
                                // store.dispatch("message/error", data.detail);
                            })
                            .finally(() => {
                                isLoading.value = false;
                            });

}

onMounted(async() => {
    const referrer_path = router.referrer.path;
    if (referrer_path.indexOf('/arrival/') !== 0 ) {
        console.log('pinia reset');
        // piniaMsgStore().reset();
        gcStore.reset();
        arrDateStore.reset();
    }
    await fetch();
});

const current = (data) => {
    currentList.value = data.response;
}


// 詳細画面を表示する。
const toDssPage = (arrivalDate, vendor_id) => {
    arrDateStore.arrivalDate = arrivalDate;
    arrDateStore.vendorId = vendor_id;
    router.push({
        name: "ArrivalLogDss"
    });
}
</script>
<template>
    <article>
            <v-form rounded class="form_sheet pa-4">
                <v-row>
                    <v-col cols="3">
                        <v-text-field label="商品名(一部)" v-model="itemname"></v-text-field>
                    </v-col>
                    <v-col cols="4">
                        <date-range-input label="入荷日" v-model:start="startDate" v-model:end="endDate"></date-range-input>
                    </v-col>
                    <v-col>
                        <run-button text="検索する" @action="fetch"></run-button>
                    </v-col>
                </v-row>
            </v-form>
    </article>
    <article class="mt-10" v-show="resultCount != 0">
        <h2 class="text-title-medium">件数：{{ resultCount }}件</h2>
        <v-table class="item_list mt-4 border-thin">
            <thead>
                <tr>
                    <th width="10%">入荷日</th>
                    <th width="15%">取引先</th>
                    <th>カード情報</th>
                    <th width="10%" class="text-center">入荷数</th>
                    <th width="10%" class="text-center">原価額</th>
                    <th class="one wide"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(r, index) in paginatedList" :key="index">
                    <td>{{ r.arrival_date }}</td>
                    <td><vendortag :vendor="r.vendor"></vendortag></td>
                    <td>
                        <card-layout :card="r.card" :lang="r.card.lang"></card-layout>
                    </td>
                    <td  class="text-center">
                        {{r.item_count}}点
                    </td>
                    <td  class="text-center">
                        &yen;{{ r.sum_cost }}
                    </td>
                    <td class="text-right">
                        <link-icon-button @action="toDssPage(r.arrival_date, r.vendor.id)"></link-icon-button>
                    </td>
                </tr>
            </tbody>
            <tfoot class="full-width">
                <tr>
                    <td colspan="6">
                       <ListPagination v-model="page" :length="pageCount"></ListPagination>
                    </td>
                </tr>
            </tfoot>
        </v-table>
    </article>
    <loading
         :active="isLoading"
         :can-cancel="false" :is-full-page="true" />
</template>
<style scoped>
.middle {
    align-items: center;display: flex;
}
</style>
