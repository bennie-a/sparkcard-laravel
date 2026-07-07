<script setup>
import { onMounted, ref, shallowRef, watch } from 'vue';
import { useRouter } from "vue-router";
import vendortag from "../component/tag/VendorTag.vue"
import axios from 'axios';
import UseDateFormatter from '../../functions/UseDateFormatter.js';
import DateRangeInput from '../component/date/DateRangeInput.vue';

import {groupConditionStore} from "@/stores/arrival/GroupCondition";
import {arrDateConditionStore} from "@/stores/arrival/arrDateCondition";

import { storeToRefs } from 'pinia';

import RunButton from '../component/button/RunButton.vue';
import CardLayout from '../component/CardLayout.vue';
import { usePaginate } from '../component/pagination/UsePaginate';
import LinkIconButton from '../component/button/LinkIconButton.vue';

import {MsgStore} from "../component/msg/MsgStore";
import PaginatedTable from '../component/pagination/PaginatedTable.vue';
import {LoadStore} from "@/stores/loading/LoadStore.js";

const loadStore = LoadStore();
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
} = usePaginate(result, 10);

const msgStore = MsgStore();

// 入荷情報検索
const fetch =  async () => {
    loadStore.on();
    result.value = [];
    resultCount.value = 0;
    msgStore.clear();
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
                    msgStore.error(data.detail);
                })
                .finally(() => {
                    loadStore.off();
                });
}

onMounted(async() => {
    const referrer_path = router.referrer.path;
    if (referrer_path.indexOf('/arrival/') !== 0 ) {
        console.log('pinia reset');
        gcStore.reset();
        arrDateStore.reset();
    }
    await fetch();
});

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
    <article class="mt-10">
        <PaginatedTable v-model="page" :items="paginatedList" :page-count="pageCount" :hit-count="resultCount">
            <template #header>
                    <th width="10%">入荷日</th>
                    <th width="15%">取引先</th>
                    <th>カード情報</th>
                    <th width="8%" class="text-center">入荷数</th>
                    <th width="8%" class="text-center">原価額</th>
                    <th width="7%"></th>
            </template>
                <template #row="{  item }">
                    <td>{{ item.arrival_date }}</td>
                    <td><vendortag :vendor="item.vendor"></vendortag></td>
                    <td>
                        <card-layout :card="item.card" :lang="item.card.lang"></card-layout>
                    </td>
                    <td  class="text-center">
                        {{item.item_count}}点
                    </td>
                    <td  class="text-center">
                        &yen;{{ item.sum_cost }}
                    </td>
                    <td class="text-right">
                        <link-icon-button @action="toDssPage(item.arrival_date, item.vendor.id)"></link-icon-button>
                    </td>
                </template>
        </PaginatedTable>
    </article>
</template>
<style scoped>
.middle {
    align-items: center;display: flex;
}
</style>
