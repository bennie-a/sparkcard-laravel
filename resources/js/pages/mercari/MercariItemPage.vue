<template>
    <v-form rounded class="form_sheet pa-4">
        <v-text-field label="セット略称" class="w-25"
            append-inner-icon="mdi-magnify"
            @click:append-inner="search"></v-text-field>
    </v-form>
    <!-- <search-form limitprice="50" status="ショップ登録予定"></search-form> -->
    <div class="mt-2" v-if="this.result.length != 0">
        <div class="ui toggle checkbox mr-2">
            <input type="checkbox" name="public" v-model="isPublic" />
            <label>メルカリに公開する</label>
        </div>
        <download-button filename="mercari_item" v-model:isDisabled="isDisabled" v-model:card="selectedCard"
            >登録・更新用CSVを作成する</download-button>
    </div>
        <article class="mt-2" v-if="this.result.length > 0">
        <h2 class="text-title-medium">件数：{{ this.result.length }}件</h2>
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
                <tr v-for="(card, index) in this.paginatedList" :key="index">
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
                        <condition :name="card.condition"></condition>
                    </td>
                    <td class="text-center">&yen;{{ card.price }}</td>
                </tr>
            </tbody>
            <tfoot
            >
                <tr>
                    <th colspan="5">
                       <ListPagination v-model="this.page" :length="this.pageCount"></ListPagination>
                    </th>
                </tr>
            </tfoot>
        </v-table>
    </article>
            <Loading
     :active="isLoading"
     :can-cancel="false" :is-full-page="true" />
</template>
<script>
import CardList from "../component/CardList.vue";
import MessageArea from "../component/msg/MessageArea.vue";
import SearchForm from "../component/SearchForm.vue";
import CSVUpload from "../component/CSVUpload.vue";
import DownloadButton from "../component/DownloadButton.vue";
import { MsgStore } from "../component/msg/MsgStore.js";
import NotionCardProvider from "../../composables/NotionCardProvider.js";
import Loading from "vue-loading-overlay";
import CardLayout from "../component/CardLayout.vue";
import ConditionTag from "../component/tag/ConditionTag.vue";
import ListPagination from "../component/pagination/ListPagination.vue";
import { usePagenate } from "../component/pagination/UsePaginate";
import { ref } from "vue";

export default {
    components: {
        "card-list": CardList,
        "message-area": MessageArea,
        "search-form": SearchForm,
        "download-button": DownloadButton,
        "Loading":Loading,
        "CardLayout":CardLayout,
        "condition":ConditionTag,
        "ListPagination":ListPagination
    },
    setup() {
        const result = ref([]);

        const {
            page,
            pageCount,
            paginatedList,
            resetPage
        } = usePagenate(result, 10);

        return {
            result,
            page,
            pageCount,
            paginatedList,
            resetPage
        };
    },
    data() {
        return {
            isPublic: true,
            isLoading:false,
            selectedCard: [],
            isAll: false,
            isDisabled:this.selectedCard == 0,
        };
    },
    methods: {
        allChecked: function () {
            if (this.isAll) {
                this.result.forEach((c) => {
                    this.selectedCard.push(c);
                });
            } else {
                this.selectedCard.splice(0);
            }
        },
        search:async function() {
            const msgStore = MsgStore();
            msgStore.clear();
            this.resetPage();
            this.isLoading = true;
            const provider = new NotionCardProvider();
            const query = {
                params:{
                    price: 300,
                    status:'ショップ登録予定',
                    set_name:this.set_name
                }
            };

            this.result = await provider.searchByStatus(query);
            this.isLoading = false;
        }
    },

};
</script>
