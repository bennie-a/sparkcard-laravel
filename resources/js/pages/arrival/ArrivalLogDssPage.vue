<script setup>
import { useRoute, useRouter } from "vue-router";
import cardlayout from "../component/CardLayout.vue";
import vendortag from "../component/tag/VendorTag.vue"
import ConditionTag from "../component/tag/ConditionTag.vue";
import {groupConditionStore} from "@/stores/arrival/GroupCondition";
import {arrDateConditionStore} from "@/stores/arrival/arrDateCondition";
import { onMounted, reactive } from "vue";
import {apiService} from "@/component/ApiGetService";
import { apiDeleteService } from "@/component/ApiDeleteService";

import {ref} from 'vue';
import Loading from "vue-loading-overlay";
import pglist from "../component/PgList.vue";
import ModalButton from "../component/modal/ModalButton.vue";
// import PiniaMsgForm from "../component/PiniaMsgForm.vue";
import { storeToRefs } from "pinia";

const router = useRouter();

const gcStore = groupConditionStore();
const arrDateStore = arrDateConditionStore();

const {arrivalDate, vendorId} = storeToRefs(arrDateStore);

// const piniaMsg = piniaMsgStore();
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
    <section v-show="!isLoading"  v-if="result.value">
        <article>
            <v-row class="w-50" gap="0">
                <v-col cols="3">入荷先カテゴリ</v-col>
                <v-col>
                    <vendortag :vendor="result.value.data.vendor"></vendortag>
                </v-col>
            </v-row>
            <v-row class="w-50" gap="10">
                <v-col cols="4">取引先名</v-col>
                <v-col>
                    <span v-if="result.value.data.vendor.supplier == ''">&mdash;</span>
                    <span v-if="result.value.data.vendor.supplier != ''">
                        {{ result.value.data.vendor.supplier }}
                    </span>
                </v-col>
            </v-row>
        </article>
        <article class="mt-10">
            <h2 class="text-title-medium">件数：{{ resultCount }}件</h2>
            <v-table class="item_list border-thin">
                <thead>
                    <tr>
                        <th width="8%" class="text-center">入荷ID</th>
                        <th width="8%" class="text-center">在庫ID</th>
                        <th>カード情報</th>
                        <th width="8%" class="text-center">状態</th>
                        <th width="8%" class="text-center">枚数</th>
                        <th width="8%" class="text-center">原価</th>
                        <th width="16%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(log, index) in currentList.value" :key="index" v-memo="currentList.value">
                        <td class="text-center">{{log.id}}</td>
                        <td class="text-center">{{ log.stock_id }}</td>
                        <td>
                                <cardlayout v-model:card="log.card" v-model:lang="log.lang"></cardlayout>
                        </td>
                        <td class="text-center"><ConditionTag :name="log.condition"/></td>
                        <td class="text-center">{{log.quantity}}枚</td>
                        <td class="text-center">&yen;{{ log.cost }}</td>
                        <td class="text-right selectable">
                            <v-btn class="mr-4" icon="mdi-square-edit-outline" variant="text" color="teal-lighten-1" @click="toEditPage(log.id)"></v-btn>
                            <ModalButton  :msg="`入荷ID[${log.id}]を削除しますか？`" @action="deleteLog(log.id)">
                                <v-icon icon="mdi-trash-can-outline" size=""></v-icon>
                            </ModalButton>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="full-width">
                    <tr>
                        <td colspan="8">
                            <div class="right aligned">
                                <pglist ref="pglistRef" v-model:list="logs.value" @loadPage="current"></pglist>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </v-table>
            <div class="text-center mt-6">
                <v-btn variant="outlined" color="teal-lighten-1" @click="toList"><v-icon icon="mdi-chevron-double-left" start></v-icon>一覧に戻る</v-btn>
            </div>
        </article>
    </section>
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
