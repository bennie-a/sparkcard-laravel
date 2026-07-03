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
        <h2 class="text-title-medium">件数：{{ result.length }}件</h2>
        <v-table  class="item_list mt-4 border-thin">
            <thead>
                <tr>
                    <th width="5%">
                        <input
                            type="checkbox"
                            id="all"
                            v-model="isAll"
                            @change="allChecked"
                        />
                    </th>
                    <th width="45%">カード情報</th>
                    <th width="8%" class="text-center">枚数</th>
                    <th width="8%" class="text-center">
                        状態
                    </th>
                    <th width="8%" class="text-center">価格</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(card, index) in paginatedList" :key="index">
                    <td>
                        <input
                            type="checkbox"
                            v-model="selectedCard"
                            :value="card"
                            @change="checked"
                        />
                    </td>
                    <td>
                        <CardLayout :card="card" :lang="card.lang"></CardLayout>
                    </td>

                    <td class="text-center">
                        {{ card.stock }}枚
                    </td>
                    <td class="text-center">
                        <Condition :name="card.condition"></Condition>
                    </td>
                    <td class="text-center">&yen;{{ card.price }}</td>
                </tr>
            </tbody>
            <tfoot
            >
                <tr>
                    <th colspan="5">
                       <ListPagination v-model="page" :length="pageCount"></ListPagination>
                    </th>
                </tr>
            </tfoot>
        </v-table>
    </article>
            <Loading
     :active="isLoading"
     :can-cancel="false" :is-full-page="true" />
</template>
<script setup>
import DownloadButton from "../component/DownloadButton.vue";
import { MsgStore } from "../component/msg/MsgStore.js";
import NotionCardProvider from "../../composables/NotionCardProvider.js";
import Loading from "vue-loading-overlay";
import CardLayout from "../component/CardLayout.vue";
import Condition from "../component/tag/ConditionTag.vue";
import ListPagination from "../component/pagination/ListPagination.vue";
import { usePaginate } from "../component/pagination/UsePaginate";
import { ref } from "vue";
import { fa } from "vuetify/locale";

const setname = ref("");
const result = ref([]);
const {
    page, pageCount, paginatedList, resetPage
} = usePaginate(result, 10);
const filename = ref('base_item');
const isLoading = ref(false);

const selectedCard = ref([]);
const isAll = ref(false);
const isDisabled = ref(true);
const search = async() => {
    const msgStore = MsgStore();
    msgStore.clear();
    resetPage();
    try {
        isLoading.value = true;
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
        isLoading.value = false;
    }
}

const allChecked = () => {
    if (isAll.value) {
        isDisabled.value = false;
        result.value.forEach((c) => {
            selectedCard.value.push(c);
        });
    } else {
        selectedCard.value.splice(0);
        isDisabled.value = true;
    }
}

const checked = () => {
    isDisabled.value = selectedCard.value.length === 0;
}
</script>
