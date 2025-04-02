<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from "vue-router";
import scdatepicker from "../component/SCDatePicker.vue";
import vendortag from "../component/tag/VendorTag.vue"
import Loading from "vue-loading-overlay";
import axios from 'axios';
import UseDateFormatter from '../../functions/UseDateFormatter.js';
import { useStore } from 'vuex';
import pglist from "../component/PgList.vue";
import MessageArea from "../component/MessageArea.vue";
import foiltag from "../component/tag/FoilTag.vue";

// 検索条件
const itemname = ref("");
const date = ref("");
const startD  = new Date();
startD.setDate(startD.getDate() - 7);
const startDate = ref(startD);
const endDate = ref(new Date());

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
    <message-area />
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
        <h2 class="ui medium dividing header">
            件数：{{resultCount}}件
        </h2>
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