<script setup>
    import FileUpload from "../component/FileUpload.vue";
    import ListPagination from "../component/pagination/ListPagination.vue";
    import ModalButton from "../component/modal/ModalButton.vue";
    import { AxiosTask } from "../../component/AxiosTask";
    import FoilTag from "../component/tag/FoilTag.vue";
    import ColorTag from "../component/tag/ColorTag.vue";
    import PromoDropdown from "../component/selection/PromoDropdown.vue";
    import ColorDropdown from "../component/selection/ColorDropdown.vue";
    import SelectAllCheckbox from "../component/selection/SelectAllCheckbox.vue";

    import {computed, ref, watch} from 'vue';
    import axios from "axios";
    import {LoadStore} from "@/stores/loading/LoadStore.js";
    import { useRoute } from "vue-router";
    import {MsgStore} from "../../pages/component/msg/MsgStore";
    import PaginatedTable from "../component/pagination/PaginatedTable.vue";
    import { usePaginate } from "../component/pagination/UsePaginate";
    import { ColorMaster } from "../component/const/ColorMaster.js";
import { filter } from "lodash";

    const isDraftOnly = ref(false);
    const color = ref("");
    const loadStore = LoadStore();
    const msgStore = MsgStore();
    const route = useRoute();

    const tab = ref(ColorMaster.MAP.W.key);

    const items = ref([]);
    const itemCount = ref(0);
    let attr = route.query.attr;

    const selected = ref([]);

    const filterdItems = computed(() => {
        return items.value.filter((value) => {
            return value.color === tab.value;
        });
    });

    const filteredCount = computed(() => {
        return filterdItems.value.length;
    });
    const disabled = computed(() => selected.value.length === 0);
    const {
        page, pageCount, paginatedList, resetPage
    } = usePaginate(filterdItems, 10);


    const upload = async(file) => {
        loadStore.on();
        try {
            resetPage();
            itemCount.value = 0;
            msgStore.clear();
            items.value.length = 0;

            const query =
                `?isDraft=${isDraftOnly.value}&color=${color.value}&setcode=${attr}`;

            const response = await axios.post(
                "/api/upload/card" + query,
                file,
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                }
            );

            if (response.status === 201) {
                items.value = response.data.cards;
                itemCount.value = items.value.length;
            }

        } catch (e) {
            msgStore.error(e.response.data.detail);
        } finally {
            loadStore.off();
        }
    }

    const store = async() => {

    }

    // タブ切り替え時にページネーションをリセット。
    watch(tab, () => {
        resetPage();
    });
// // export default {
// //     components: {
// //         "file-upload": FileUpload,
// //         "loading":Loading,
// //         pagination: ListPagination,
// //         ModalButton: ModalButton,
// //         foiltag: FoilTag,
// //         promo:PromoDropdown
// //     },
// //     data() {
// //         return {
// //             isSkip: false,
// //             isLoading: false,
// //             isDraftOnly: false,
// //             color: "",
// //             promoItems:[],
// //             name:ref("通常版"),
// //             checkedCard:[]
// //         };
// //     },
// //     computed: {
// //         getCards: function () {
// //             return this.$store.getters.sliceCard;
// //         },
// //         join: function () {
// //             return function (key) {
// //                 return key.join("|");
// //             };
// //         },
// //     },

// //     methods: {
// //         store: async function () {
// //             this.isLoading = true;
// //             this.$store.dispatch("message/clear");
// //             this.$store.dispatch("clearMessage");

// //             if (this.checkedCard.length == 0) {
// //                 this.isLoading = false;
// //                 this.$store.dispatch("message/error", "登録するカードを選択してください。");
// //                 return;
// //             }

// //             const task = new AxiosTask(this.$store);
// //             const list = this.$store.getters.card;
// //             await Promise.all(
// //                 list.map(async (card) => {
// //                     if (this.checkedCard.includes(card.number) == false) {
// //                         return;
// //                     }
// //                     if (card.name != "") {
// //                         const success = function (response, store) {};
// //                         card["is_skip"] = this.isSkip;
// //                         await task.post("/database/card", card, success);
// //                     }
// //                 })
// //             ).catch(() => {
// //                 console.error("error");
// //             });
// //             this.isLoading = false;
// //             this.$store.dispatch(
// //                 "setSuccessMessage",
// //                 `${this.checkedCard.length}件登録が完了しました。`
// //             );

// //             console.log("store finished.");
// //         },
// //     },
// };
</script>
<template>
    <v-form rounded class="form_sheet pa-4">
        <v-row  gap="10" class="align-center">
            <v-col cols="2" class="mr-5">
                <v-switch v-model="isDraftOnly" label="通常版のみ表示" color="teal-lighten-1" true-icon="mdi-check"
            false-icon="mdi-close"></v-switch>
            </v-col>
            <!-- <v-col cols="3" class="mr-5">
                <color-dropdown v-model="color"></color-dropdown>
            </v-col> -->
            <v-col cols="5">
                <file-upload @action="upload" type="json" icon="mdi-code-json"></file-upload>
            </v-col>
        </v-row>
    </v-form>

    <article class="mt-10" v-if="itemCount > 0">
        <v-sheet class="border-thin">
        <v-tabs v-model="tab" color="teal-lighten-1">
          <v-tab
            v-for="item in ColorMaster.list"
            :prepend-icon="item.icon"
            :key="item.key"
            :text="item.key"
            :value="item.key"
            :bg-color="item.color"
          ></v-tab>
        </v-tabs>
      <v-divider class="mx-1"></v-divider>
        <v-tabs-window v-model="tab">
            <v-tabs-window-item v-for="item in ColorMaster.list" :key="item.key" :value="item.key" class="pa-5">
                <span v-if="filteredCount == 0">カード情報がありません。</span>
                <paginated-table v-model="page" :items="paginatedList" :page-count="pageCount" :hit-count="filteredCount" v-else>
                    <template #header>
                        <th width="5%">
                            <select-all-checkbox v-model:selected="selected" v-model:result="items"></select-all-checkbox>
                        </th>
                        <th width="5%" class="text-center">No.</th>
                        <th>カード名</th>
                        <th width="30%">特別版</th>
                        <th width="18%">仕上げ</th>
                    </template>
                    <template #row="{ item }">
                        <td>
                            <v-checkbox-btn color="teal-lighten-1" v-model="selected"
                                :value="item"></v-checkbox-btn>
                        </td>
                        <td class="text-center">{{ item.number }}</td>
                        <td class="pt-1">
                            <span class="text-medium-emphasis">{{ item.en_name }}</span>
                            <v-text-field v-model="item.name"></v-text-field>
                        </td>
                        <td class="pt-5">
                            <PromoDropdown v-model:id="item.promotype_id" v-model:setcode="attr"></PromoDropdown>
                        </td>
                        <td>
                            <v-chip v-for="f in item.foiltype" :key="f"  class="mr-4" label density="compact">{{f}}</v-chip>
                        </td>
                    </template>
                </paginated-table>
            </v-tabs-window-item>
        </v-tabs-window>
        </v-sheet>
        <div class="text-center mt-6">
            <!-- <div
                class="three wide column middle aligned content ui toggle checkbox"
            >
                <input type="checkbox" id="isSkip" v-model="isSkip" />
                <label for="isSkip">更新をスキップ</label>
            </div> -->
            <div class="three wide column">
                <ModalButton @action="store" v-model="disabled">DBに登録する</ModalButton>
            </div>
        </div>
    </article>
</template>

<style>
.wall {
    background-color: white;
    padding: 1em;
}
</style>
