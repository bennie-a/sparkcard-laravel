<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from "vue-router";
import scdatepicker from "../component/SCDatePicker.vue";
import vendortag from "../component/tag/VendorTag.vue"
import Loading from "vue-loading-overlay";
import axios from 'axios';
import { useStore } from 'vuex';
import UseDateFormatter from '../../functions/UseDateFormatter.js';

import pglist from "../component/PgList.vue";
import PiniaMsgForm from "../component/PiniaMsgForm.vue";
import foiltag from "../component/tag/FoilTag.vue";
import {groupConditionStore} from "@/stores/arrival/GroupCondition";
import { piniaMsgStore } from '@/stores/global/PiniaMsg.js';
import { storeToRefs } from 'pinia';

const gcStore = groupConditionStore();

// 検索条件
const {startDate, endDate, itemname} = storeToRefs(gcStore);

// 検索結果
let result = reactive([]);
const resultCount = ref(0);
const currentList = reactive([]);
const isLoading = ref(false);


const router = useRouter();
const {toString} = UseDateFormatter();
const store = useStore();

// 入荷情報検索
const fetch =  async () => {
    isLoading.value = true;
    result.value = [];
    resultCount.value = 0;
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
                                store.dispatch("message/error", data.detail);
                            })
                            .finally(() => {
                                isLoading.value = false;
                            });    

}

onMounted(async() => {
    const referrer_path = router.referrer.path;
    if (referrer_path.indexOf('/arrival/') !== 0 ) {
        console.log('pinia reset');
        gcStore.reset();
        piniaMsgStore().reset();
    }
    await fetch();
});

const current = (data) => {
    currentList.value = data.response;
}


// 詳細画面を表示する。
const toDssPage = (arrivalDate, vendor_id) => {
    router.push({
        name: "ArrivalLogDss",
        params: { arrival_date: arrivalDate, vendor_id:vendor_id},
    });
}
</script>
<template>
    <PiniaMsgForm></PiniaMsgForm>
    <article class="mt-1 ui form segment">
        <div class="three fields">
            <div class="four wide field">
                <label>商品名(一部)</label>
                <input v-model="itemname" type="text">
            </div>
            <div class="six wide field">
                <label for="">入荷日</label>
                <div class="three fields">
                    <div class="seven wide field">
                        <scdatepicker v-model="startDate"></scdatepicker>
                    </div>
                    <div class="one wide field middle">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                    <div class="seven wide field">
                        <scdatepicker v-model="endDate"></scdatepicker>
                    </div>
                </div>
            </div>
            <div class="field">
                <label class="hidden">ボタン</label>
                <button
                    id="search" class="ui button teal" @click="fetch">
                    検索
                    </button>
            </div>
        </div>
    </article>
    <article class="mt-2" v-show="resultCount != 0">
        <h3 class="ui devide">{{ resultCount }}件</h3>
        <table class="ui striped table">
            <thead>
                <tr>
                    <th class="two wide center aligned">入荷日</th>
                    <th class="" colspan="2">取引先</th>
                    <th class="">商品名</th>
                    <th class="two wide center aligned">入荷数</th>
                    <th class="two wide center aligned">原価合計</th>
                    <th class="one wide"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(r, index) in currentList.value" :key="index">
                    <td class="center aligned">{{ r.arrival_date }}</td>
                    <td colspan="2"><vendortag v-model="r.vendor"></vendortag><span class="ml-half">{{ r.vendor.supplier }}</span></td>
                    <td>
                        <foiltag :isFoil="r.card.foil.is_foil" :foiltype="r.card.foil.name"></foiltag>
                        【{{r.card.exp.attr}}】{{r.card.name}}[{{ r.card.lang }}]<span v-if="r.item_count !== 1">ほか</span>
                    </td>
                    <td class="center aligned">
                        {{r.item_count}}点
                    </td>
                    <td class=" center aligned"><i class="bi bi-currency-yen"></i>{{ r.sum_cost }}</td>
                    <td class="center aligned selectable">
                        <a @click="toDssPage(r.arrival_date, r.vendor.id)">
                            <i class="angle double right icon"></i>
                        </a>
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
.middle {
    align-items: center;display: flex;
}
</style>