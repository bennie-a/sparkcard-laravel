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
import PaginatedTable from "../component/pagination/PaginatedTable.vue";
import {LoadStore} from "@/stores/loading/LoadStore.js";
import * as yup from 'yup';
import { useField, useForm } from "vee-validate";

const cardname = ref("");
const stock = ref([]);
const stockCount = ref(0);
const {
    page, pageCount, paginatedList, resetPage
} = usePaginate(stock, 10);

const msgStore = MsgStore();
const loadStore = LoadStore();

const schema = yup.object({
    setname:yup.string().label('セット略称').customAlpha()
});

const {handleSubmit} = useForm({
    validationSchema:schema,
    initialValues:{
        setname:''
    }
});

const {value:setname, errorMessage:SetErr} = useField('setname');

const search = handleSubmit(
    async () => {
        try {
            if (cardname.value === "" && setname.value === "") {
                msgStore.error("カード名かセット略称のどちらかを入力してください。");
                return;
            }
            loadStore.on();
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
            loadStore.off();
        }
    }
);
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
                    <v-text-field v-model="setname" label="セット略称" :error-messages="SetErr">
                    </v-text-field>
                </v-col>
                <v-col cols="2" class="text-right">
                    <run-button text="検索する" @action="search"></run-button>
                </v-col>
            </v-row>
        </v-form>
    </article>
    <article class="mt-10">
        <PaginatedTable v-model="page" :items="paginatedList" :page-count="pageCount" :hit-count="stockCount">
            <template #header>
                <th width="10%">在庫ID</th>
                <th>カード情報</th>
                <th width="10%" class="text-center">色</th>
                <th width="10%" class="text-center">状態</th>
                <th width="10%" class="text-center">枚数</th>
                <th width="10%" >最終更新日</th>
            </template>
            <template #row="{  item }">
                <td>{{item.id }}</td>
                <td>
                    <CardLayout :card="item.card" :lang="item.lang"></CardLayout>
                </td>
                <td class="text-center">
                    <ColorTag :type="item.card.color" />
                </td>
                <td class="text-center">
                    <condition :name="item.condition"/>
                </td>
                <td class="text-center">{{ item.quantity }}枚</td>
                <td class="text-center">{{ item.updated_at }}</td>
            </template>
        </PaginatedTable>
    </article>
</template>
