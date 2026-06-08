<script setup>
import axios from "axios";
import ListPagination from "../component/pagination/ListPagination.vue";
import condition from "../component/tag/ConditionTag.vue";
import ImageModal from "../component/modal/ImageModal.vue";
import CardLayout from "../component/CardLayout.vue";
import Loading from "vue-loading-overlay";
import { ref } from "vue";
import { usePagenate } from "../component/pagination/UsePaginate";

const isLoading = ref(false);
const cardname = ref("");
const setname = ref("");
const stock = ref([]);
const stockCount = ref(0);

    const {
        page, pageCount, paginatedList, resetPage
    } = usePagenate(stock, 10);

const search = async () => {
    isLoading.value = true;
    stockCount.value = 0;
    resetPage();
    const query = {
        params: {
            card_name: cardname.value,
            set_name: setname.value,
        },
    };
    await axios
        .get("/api/stockpile", query)
        .then((response) => {
            console.log(response.data);
            let data = response.data;
            stock.value = data;
            stockCount.value = stock.value.length;
        })
        .catch((e) => {
            let data = e.response.data;
            console.error(data);
            this.$store.dispatch(
                    "message/error",
                    data.detail
                );
        })
        .finally(() => {
            isLoading.value = false;
        });
    }
// export default {
//     components: {
//         loading: Loading,
//         pagination: ListPagination,
//         foiltag: FoilTag,
//         condition: ConditionTag,
//         cardlayout:CardLayout,
//         "image-modal":ImageModal
//     },
//     data() {
//         return {
//             cardname: "",
//             setname: "",
//             isLoading: true,
//             stock: [],
//         };
//     },
//     methods: {
//         async search() {
//             this.$store.dispatch("message/clear");
//             this.$store.dispatch("clearCards");
//             this.isLoading = true;
//             console.log("start search stockpile");
//             const query = {
//                 params: {
//                     card_name: this.cardname,
//                     set_name: this.setname,
//                 },
//             };
//             await axios
//                 .get("/api/stockpile", query)
//                 .then((response) => {
//                     console.log(response.data);
//                     let data = response.data;
//                     this.stock = data;
//                     this.$store.dispatch("setCard", this.stock);
//                 })
//                 .catch((e) => {
//                     let data = e.response.data;
//                     console.error(data);
//                     this.$store.dispatch(
//                             "message/error",
//                             data.detail
//                         );
//                 })
//                 .finally(() => {
//                     this.isLoading = false;
//                     console.log("end search stockpile");
//                 });
//         },
//     },
// };
</script>

<template>
    <article>
        <v-form rounded class="form_sheet pa-4">
            <v-row gap="15">
                <v-col cols="3">
                    <v-text-field v-model="cardname"  label="カード名(一部)">
                    </v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field v-model="setname" label="セット略称">
                    </v-text-field>
                </v-col>
                <v-col cols="2" class="text-right">
                    <v-btn color="teal-lighten-1" @click="search">
                        検索する
                    </v-btn>
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
                    <th width="15%" class="text-center">色</th>
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
                        <v-chip label>{{ s.card.color }}</v-chip>
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
