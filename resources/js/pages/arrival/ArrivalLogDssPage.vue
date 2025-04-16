<script setup>
import { useRoute, useRouter } from "vue-router";
import cardlayout from "../component/CardLayout.vue";
import pagination from "../component/ListPagination.vue";
import vendortag from "../component/tag/VendorTag.vue"
import ConditionTag from "../component/tag/ConditionTag.vue";
import {groupConditionStore} from "@/stores/arrival/GroupCondition";
import { onMounted, reactive } from "vue";
import {apiService} from "@/component/ApiGetService";

import {ref} from 'vue';
import Loading from "vue-loading-overlay";
import pglist from "@/pages/component/PgList.vue";


const route = useRoute();
const router = useRouter();

const arrival_date = route.params.arrival_date;
const vendor_id = route.params.vendor_id;
const gcStore = groupConditionStore();
const currentList = reactive([]);
const isLoading = ref(false);

const result = reactive([]);
onMounted(async() =>{
    isLoading.value = true;
    await apiService.get(
        {
            url:"/arrival/",
            query:{params:{
                arrival_date:arrival_date,
                vendor_type_id:vendor_id,
                card_name:gcStore.itemname
            }},
            onSuccess:(data) => {
                result.value = data;
            },
            onFinally:() => {
                isLoading.value = false;
            }
        });
    });

// 入荷情報一覧ページに戻る
const toList = () => {
    router.push("/arrival");
}

// 入荷情報編集ページに遷移する
 const toEditPage = (arrival_id) => {
        router.push({
            name: "ArrivalLogEdit",
            params: { arrival_date:arrival_date, arrival_id: arrival_id},
        });
    }

 const current = (data) => {
    currentList.value = data.response;
}

</script>
<template>
    <article v-show="!isLoading">
        <h3 class="ui header">入荷情報</h3>
        <table class="ui collapsing definition table" v-if="result.length != 0">
            <tr>
                <td>入荷先カテゴリ</td>
                <td>
                    <vendortag v-model="result[0].vendor"></vendortag>
                </td>
            </tr>
            <tr>
                <td>取引先名</td>
                <td class="center aligned">
                    <span v-if="result[0].vendor.supplier == ''">&mdash;</span>
                    <span v-if="result[0].vendor.supplier != ''">
                        {{ result[0].vendor.supplier }}
                    </span>
                </td>
            </tr>
        </table>
    </article>
    <article class="mt-3" v-show="!isLoading">
        <h3 class="ui header">商品一覧</h3>
        <table class="ui striped table">
            <thead>
                <tr>
                    <th class="two wide center aligned">入荷ID</th>
                    <th class="seven wide">商品名</th>
                    <th class="center aligned">状態</th>
                    <th class="center aligned">枚数</th>
                    <th class="center aligned">原価</th>
                    <th class="one wide"></th>
                    <th class="one wide"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(log, index) in currentList.value" :key="index">
                    <td class="center aligned">{{log.id}}</td>
                    <td>
                            <cardlayout v-model="log.card"></cardlayout>
                    </td>
                    <td class="center aligned"><ConditionTag :name="log.card.condition"/></td>
                    <td class="center aligned">{{log.quantity}}枚</td>
                    <td class="center aligned"><i class="bi bi-currency-yen"></i>{{ log.cost }}</td>
                    <td class="center aligned selectable">
                        <a @click="toEditPage(log.id)"><i class="edit icon"></i></a>
                    </td>
                    <td class="center aligned selectable">
                        <a class="icon"><i class="trash alternate outline icon"></i></a>
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
        <div class="text-center">
            <button class="ui gray basic button" @click="toList">一覧に戻る</button>
        </div>
    </article>
    <loading
         :active="isLoading"
         :can-cancel="false" :is-full-page="true" />
</template>