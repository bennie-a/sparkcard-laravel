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
import { storeToRefs } from "pinia";

import { usePaginate } from '../component/pagination/UsePaginate';
import ListPagination from '../component/pagination/ListPagination.vue';
import PaginatedTable from "../component/pagination/PaginatedTable.vue";

const router = useRouter();

const gcStore = groupConditionStore();
const arrDateStore = arrDateConditionStore();

const {arrivalDate, vendorId} = storeToRefs(arrDateStore);

const currentList = reactive([]);
const resultCount = ref(0);

const logs = ref([]);
const isLoading = ref(false);

    const {
        page, pageCount, paginatedList, resetPage
    } = usePaginate(logs, 10);

const result = reactive([]);
onMounted(async() =>{
    isLoading.value = true;
    await fetch();
    });

const fetch = async() => {
    resetPage();
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
                <v-col cols="3">取引先名</v-col>
                <v-col>
                    <span v-if="result.value.data.vendor.supplier == ''">&mdash;</span>
                    <span v-if="result.value.data.vendor.supplier != ''">
                        {{ result.value.data.vendor.supplier }}
                    </span>
                </v-col>
            </v-row>
        </article>
        <article class="mt-10">
            <PaginatedTable v-model="page" :items="paginatedList" :page-count="pageCount" :hit-count="resultCount">
                <template #header>
                        <th width="8%" class="text-center">入荷ID</th>
                        <th width="8%" class="text-center">在庫ID</th>
                        <th>カード情報</th>
                        <th width="8%" class="text-center">状態</th>
                        <th width="8%" class="text-center">枚数</th>
                        <th width="8%" class="text-center">原価</th>
                        <th width="16%"></th>
                </template>
                <template #row="{  item }">
                        <td class="text-center">{{item.id}}</td>
                        <td class="text-center">{{ item.stock_id }}</td>
                        <td>
                                <cardlayout v-model:card="item.card" v-model:lang="item.lang"></cardlayout>
                        </td>
                        <td class="text-center"><ConditionTag :name="item.condition"/></td>
                        <td class="text-center">{{item.quantity}}枚</td>
                        <td class="text-center">&yen;{{ item.cost }}</td>
                        <td class="text-right selectable">
                            <v-btn class="mr-4" icon="mdi-square-edit-outline" variant="text" color="teal-lighten-1" @click="toEditPage(item.id)"></v-btn>
                            <ModalButton  :msg="`入荷ID[${item.id}]を削除しますか？`" @action="deleteLog(item.id)">
                                <v-icon icon="mdi-trash-can-outline" size=""></v-icon>
                            </ModalButton>
                        </td>
                </template>
            </PaginatedTable>
            <div class="text-center">
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
