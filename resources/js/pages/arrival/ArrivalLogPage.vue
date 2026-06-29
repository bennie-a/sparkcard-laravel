<script setup>
import { onMounted, reactive, ref, shallowRef } from 'vue';
import { useRouter } from "vue-router";
import vendortag from "../component/tag/VendorTag.vue"
import Loading from "vue-loading-overlay";
import axios from 'axios';
import { useStore } from 'vuex';
import UseDateFormatter from '../../functions/UseDateFormatter.js';
import DateRangeInput from '../component/date/DateRangeInput.vue';

import pglist from "../component/PgList.vue";
import PiniaMsgForm from "../component/PiniaMsgForm.vue";
import foiltag from "../component/tag/FoilTag.vue";
import {groupConditionStore} from "@/stores/arrival/GroupCondition";
import {arrDateConditionStore} from "@/stores/arrival/arrDateCondition";

import { storeToRefs } from 'pinia';

import { useDate } from 'vuetify';
import RunButton from '../component/button/RunButton.vue';

const gcStore = groupConditionStore();
const arrDateStore = arrDateConditionStore();

// 検索条件
const {startDate, endDate, itemname} = storeToRefs(gcStore);

const selectedDate = ref([]);

// 検索結果
let result = reactive([]);
const resultCount = ref(0);
const currentList = reactive([]);
const isLoading = ref(false);


const router = useRouter();
const {toString} = UseDateFormatter();
const store = useStore();

function aaa() {
    console.log(1111);
}

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
                        <v-text-field label="商品名(一部)"></v-text-field>
                    </v-col>
                    <v-col cols="4">
                        <date-range-input label="入荷日" days="7" v-model:selected-date="selectedDate"></date-range-input>
                    </v-col>
                    <v-col>
                        <run-button text="検索する" @action="aaa"></run-button>
                                        <button
                    id="search" class="ui button teal" @click="aaa">
                    検索
                    </button>

                    </v-col>
                </v-row>
            </v-form>
        <div class="three fields">
            <div class="four wide field">
                <label>商品名(一部)</label>
                <input v-model="itemname" type="text">
            </div>
            <div class="six wide field">
                <!-- <div class="three fields">
                    <div class="seven wide field">
                        <scdatepicker v-model="startDate"></scdatepicker>
                    </div>
                    <div class="one wide field middle">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                    <div class="seven wide field">
                        <scdatepicker v-model="endDate"></scdatepicker>
                    </div>
                </div> -->
            </div>
            <div class="field">
                <label class="hidden">ボタン</label>
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
                    <td class=" center aligned">
                        ¥{{ r.sum_cost }}
                    </td>
                    <td class="center aligned selectable">
                        <a @click="toDssPage(r.arrival_date, r.vendor.id)">
                        <v-icon icon="mdi-chevron-double-right"></v-icon>
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
