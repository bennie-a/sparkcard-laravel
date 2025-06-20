<script setup>
import { useRoute, useRouter } from "vue-router";
import cardlayout from "../component/CardLayout.vue";
import vendortag from "../component/tag/VendorTag.vue"
import ConditionTag from "../component/tag/ConditionTag.vue";
import {groupConditionStore} from "@/stores/arrival/GroupCondition";
import {arrDateConditionStore} from "@/stores/arrival/arrDateCondition";
import { piniaMsgStore } from "@/stores/global/PiniaMsg";
import { onMounted, reactive } from "vue";
import {apiService} from "@/component/ApiGetService";
import { apiDeleteService } from "@/component/ApiDeleteService";

import {ref} from 'vue';
import Loading from "vue-loading-overlay";
import pglist from "../component/PgList.vue";
import ModalButton from "../component/ModalButton.vue";
import PiniaMsgForm from "../component/PiniaMsgForm.vue";
import { storeToRefs } from "pinia";

const router = useRouter();

const gcStore = groupConditionStore();
const arrDateStore = arrDateConditionStore();

const {arrivalDate, vendorId} = storeToRefs(arrDateStore);

const piniaMsg = piniaMsgStore();
const currentList = reactive([]);
const resultCount = ref(0);

const logs = reactive([]);
const isLoading = ref(false);

const result = reactive([]);
onMounted(async() =>{
    isLoading.value = true;
    await fetch();
    });

const fetch = async() => {
    await apiService.get(
        {
            url:"/arrival/",
            query:{params:{
                arrival_date:arrivalDate.value,
                vendor_type_id:vendorId.value,
                card_name:gcStore.itemname
            }},
            onSuccess:(data) => {
                result.value = data;
                resultCount.value = result.value.logs.length;
                logs.value = result.value.logs;
            },
            onFinally:() => {
                isLoading.value = false;
            }
        });
}

// 入荷情報一覧ページに戻る
const toList = () => {
    router.push("/arrival");
}

// 入荷情報編集ページに遷移する
 const toEditPage = (arrival_id) => {
        router.push({
            name: "ArrivalLogEdit",
            params: {arrival_id: arrival_id},
        });
    }

    // 入荷情報を1件削除する。
const deleteLog = async(arrival_id) => {
    isLoading.value = true;
    piniaMsg.reset();
    await apiDeleteService.delete({
        url: "/arrival/",
         id:arrival_id,
        onSuccess: (response) => {
            piniaMsg.setSuccess("削除しました。");
            toList();
        },
        onFinally: () => {
            isLoading.value = false;
        }
    });
    }

 const current = (data) => {
    currentList.value = data.response;
}

</script>
<template>
    <article v-show="!isLoading">
        <PiniaMsgForm></PiniaMsgForm>
        <h2 class="ui header">入荷情報</h2>
        <table class="ui collapsing definition table" v-if="result.value">
            <tr>
                <td>入荷先カテゴリ</td>
                <td>
                    <vendortag v-model="result.value.data.vendor"></vendortag>
                </td>
            </tr>
            <tr>
                <td>取引先名</td>
                <td class="center aligned">
                    <span v-if="result.value.data.vendor.supplier == ''">&mdash;</span>
                    <span v-if="result.value.data.vendor.supplier != ''">
                        {{ result.value.data.vendor.supplier }}
                    </span>
                </td>
            </tr>
        </table>
    </article>
    <article class="mt-3" v-show="!isLoading">
        <h2 class="ui header">商品一覧</h2>
        <h3 class="ui devide">{{ resultCount }}件</h3>
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
                <tr v-for="(log, index) in currentList.value" :key="index" v-memo="currentList.value">
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
                    <td class="center aligned">
                        <ModalButton  :msg="`入荷ID[${log.id}]を削除しますか？`" @action="deleteLog(log.id)">
                            <i class="trash alternate outline icon"></i>
                        </ModalButton>
                    </td>
                </tr>
            </tbody>
            <tfoot class="full-width">
                <tr>
                    <th colspan="7">
                        <div class="right aligned">
                            <pglist ref="pglistRef" v-model:list="logs.value" @loadPage="current"></pglist>
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
<style scoped>
i.icon {
    cursor: pointer;
    font-size: 1.3rem;
}
</style>