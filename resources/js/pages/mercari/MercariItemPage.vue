<template>
    <message-area></message-area>
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
        <download-button filename="mercari_item"
            ><i class="download icon"></i
            >登録・更新用CSVを作成する</download-button
        >
    </div>
        {{ this.result }}
        <article class="mt-2" v-if="this.result.length > 0">
        <h2 class="ui medium dividing header">件数：{{ this.result.length }}件</h2>
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
                <tr v-for="(card, index) in this.result" :key="index">
                    <td>
                        <input
                            type="checkbox"
                            v-model="selectedCard"
                            :value="card.id"
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
                class="full-width"
                v-if="$store.getters.cardsLength != 0"
            >
                <tr>
                    <th colspan="10">
                        <div class="right aligned">
                            <pagination></pagination>
                        </div>
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

export default {
    components: {
        "card-list": CardList,
        "message-area": MessageArea,
        "search-form": SearchForm,
        "download-button": DownloadButton,
        "Loading":Loading,
        "CardLayout":CardLayout,
        "condition":ConditionTag
    },
    data() {
        return {
            isPublic: true,
            result:[],
            isLoading:false
            // contentMap: {},
        };
    },
    methods: {
        search:async function() {
            const msgStore = MsgStore();
            msgStore.clear();
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
            console.log(this.result);
            this.isLoading = false;
        }
    },

};
</script>
