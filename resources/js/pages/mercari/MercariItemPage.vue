<template>
    <v-form rounded class="form_sheet pa-4">
        <v-text-field label="セット略称" class="w-25"
            append-inner-icon="mdi-magnify" v-model="setname"
            @click:append-inner="search"></v-text-field>
    </v-form>
    <article class="mt-10" v-if="result.length !== 0">
        <v-row>
            <v-col cols="3">
                <v-radio-group v-model="filename" inline label="プラットフォーム">
                    <v-radio label="BASE" value="base_item" color="teal-lighten-1"></v-radio>
                    <v-radio label="メルカリ" value="mercari_item" color="teal-lighten-1"></v-radio>
                </v-radio-group>
            </v-col>
            <v-col>
                <download-button v-model:filename="filename" v-model:isDisabled="isDisabled" v-model:card="selectedCard"
                    >登録用CSVを作成する</download-button>
            </v-col>
        </v-row>
        <PaginatedTable v-model="page" :items="paginatedList" :page-count="pageCount" :hit-count="result.length">
            <template #header>
                <th width="1%">
                    <select-all-checkbox v-model:selected="selectedCard" v-model:result="result"></select-all-checkbox>
                </th>
                <th width="45%">カード情報</th>
                <th width="8%" class="text-center">枚数</th>
                <th width="8%" class="text-center">
                    状態
                </th>
                <th width="8%" class="text-center">価格</th>
            </template>
            <template #row="{ item }">
                <td>
                    <v-checkbox-btn color="teal-lighten-1" v-model="selectedCard"
                        :value="item"></v-checkbox-btn>
                </td>
                <td>
                    <CardLayout :card="item" :lang="item.lang"></CardLayout>
                </td>

                <td class="text-center">
                    {{ item.stock }}枚
                </td>
                <td class="text-center">
                    <Condition :name="item.condition"></Condition>
                </td>
                <td class="text-center">&yen;{{ item.price }}</td>
            </template>
        </PaginatedTable>
    </article>
</template>
<script setup>
import DownloadButton from "../component/DownloadButton.vue";
import { MsgStore } from "../component/msg/MsgStore.js";
import NotionCardProvider from "../../composables/NotionCardProvider.js";
import CardLayout from "../component/CardLayout.vue";
import Condition from "../component/tag/ConditionTag.vue";
import ListPagination from "../component/pagination/ListPagination.vue";
import { usePaginate } from "../component/pagination/UsePaginate";
import { computed, ref } from "vue";
import { LoadStore } from "../../stores/loading/LoadStore.js";
import PaginatedTable from "../component/pagination/PaginatedTable.vue";
import SelectAllCheckbox from "../component/selection/SelectAllCheckbox.vue";

const setname = ref("");
const result = ref([]);
const {
    page, pageCount, paginatedList, resetPage
} = usePaginate(result, 10);
const filename = ref('base_item');
const loadStore = LoadStore();

const selectedCard = ref([]);
const isDisabled = computed(() => selectedCard.value.length === 0);
const search = async() => {
    const msgStore = MsgStore();
    msgStore.clear();
    resetPage();
    try {
        loadStore.on();
        const provider = new NotionCardProvider();
        const query = {
            params:{
                price: 30,
                status:'ショップ登録予定',
                set_name:setname.value
            }
        };

        result.value = await provider.searchByStatus(query);

    } catch(e) {
        result.value = [];
    } finally {
        loadStore.off();
    }
}
</script>
