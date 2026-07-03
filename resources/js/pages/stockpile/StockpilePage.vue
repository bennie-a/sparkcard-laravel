<script setup>
import axios from "axios";
import ListPagination from "../component/pagination/ListPagination.vue";
import condition from "../component/tag/ConditionTag.vue";
import ImageModal from "../component/modal/ImageModal.vue";
import CardLayout from "../component/CardLayout.vue";
import Loading from "vue-loading-overlay";
import { ref } from "vue";
import { usePaginate } from "../component/pagination/UsePaginate";
import { MsgStore } from "../component/msg/MsgStore.js";
import ColorTag from "../component/tag/ColorTag.vue";
import RunButton from "../component/button/RunButton.vue";

const isLoading = ref(false);
const cardname = ref("");
const setname = ref("");
const stock = ref([]);
const stockCount = ref(0);

const {
    page, pageCount, paginatedList, resetPage
} = usePaginate(stock, 10);

const msgStore = MsgStore();
const search = async () => {
    try {
        if (cardname.value === "" && setname.value === "") {
            msgStore.error("カード名かセット略称のどちらかを入力してください。");
            return;
        }
        isLoading.value = true;
        stockCount.value = 0;
        resetPage();
        msgStore.clear();
        const response = await axios.get("/api/stockpile", {
            params: {
                card_name: cardname.value,
                set_name: setname.value,
            },
        });
        stock.value = response.data;
        stockCount.value = stock.value.length;

    } catch (e) {
        if (e.status === 404) {
            let data = e.response.data;
            msgStore.error(data.detail);
            return;
        }
        console.error(e);
        msgStore.error("検索中にエラーが発生しました。");
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <article>
        <v-form rounded class="form_sheet pa-4">
            <v-row gap="15">
                <v-col cols="3">
                    <v-text-field v-model="cardname"  label="カード名(一部)" clearable>
                    </v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field v-model="setname" label="セット略称" clearable>
                    </v-text-field>
                </v-col>
                <v-col cols="2" class="text-right">
                    <run-button text="検索する" @action="search"></run-button>
                </v-col>
            </v-row>
        </v-form>
    </article>
    <article class="mt-10" v-if="stockCount > 0">
        <h2 class="text-title-medium">
            件数：{{ stockCount }}件
        </h2>
        <v-table class="item_list mt-4 border-thin">
            <thead>
                <tr>
                    <th width="10%">在庫ID</th>
                    <th>カード情報</th>
                    <th width="10%" class="text-center">色</th>
                    <th width="10%" class="text-center">状態</th>
                    <th width="10%" class="text-center">枚数</th>
                    <th width="10%" >最終更新日</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="s in paginatedList"
                    :key="s.id"
                >
                    <td>{{ s.id }}</td>
                    <td>
                        <CardLayout :card="s.card" :lang="s.lang"></CardLayout>
                    </td>
                    <td class="text-center">
                        <ColorTag :type="s.card.color" />
                    </td>
                    <td class="text-center">
                        <condition :name="s.condition"/>
                    </td>
                    <td class="text-center">{{ s.quantity }}枚</td>
                    <td class="text-center">{{ s.updated_at }}</td>
                </tr>
            </tbody>
            <tfoot class="full-width">
                <tr>
                    <td colspan="6">
                       <ListPagination v-model="page" :length="pageCount"></ListPagination>
                    </td>
                </tr>
            </tfoot>
        </v-table>
    </article>
    <Loading
    :active="isLoading"
    :can-cancel="false" :is-full-page="true" />
</template>
