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
    import ProgressBar from "../component/modal/ProgressBar.vue";

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

    const currentColor = computed(() => ColorMaster.find(tab.value).color);
    const dialog = ref(false);
    const finCount = ref(0);

    const upload = async(file) => {
        loadStore.on();
        try {
            resetPage();
            itemCount.value = 0;
            msgStore.clear();
            items.value = [];

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


    // 登録処理を行う。
    const store = async() => {
        dialog.value = true;
        finCount.value = 0;
        const task = new AxiosTask();
        await Promise.all(
            selected.value.map(async (card) => {
                if (card.name != "") {
                    card["is_skip"] = true;
                    card["promotype_id"] = card.promotype.id;
                    await task.post("/database/card", card);
                    finCount.value++;
                }
            }),
            msgStore.success(`${selected.value.length}件登録しました。`)

        ).catch(() => {
            console.error("error");
        }).finally(() => {
            dialog.value = false;
        });
    }

    // タブ切り替え時にページネーションをリセット。
    watch(tab, () => {
        resetPage();
    });
</script>
<template>
    <v-form rounded class="form_sheet pa-4">
        <v-row  gap="10" class="align-center">
            <v-col cols="2" class="mr-5">
                <v-switch v-model="isDraftOnly" label="通常版のみ表示" color="teal-lighten-1" true-icon="mdi-check"
            false-icon="mdi-close"></v-switch>
            </v-col>
            <v-col cols="5">
                <file-upload @action="upload" type="json" icon="mdi-code-json"></file-upload>
            </v-col>
        </v-row>
    </v-form>
    <article class="mt-10" v-if="itemCount > 0">
        <v-sheet class="border-thin">
        <v-tabs v-model="tab">
          <v-tab
            v-for="item in ColorMaster.list"
            :prepend-icon="item.icon"
            :key="item.key"
            :text="item.key"
            :value="item.key"
            :selected-class="`bg-${item.color} text-white`"
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
                        <th width="30%">プロモタイプ</th>
                        <th width="13%" class="text-center">登録済み</th>
                    </template>
                    <template #row="{ item }">
                        <td :class="{'bg-brown-lighten-5':item.isReg }">
                            <v-checkbox-btn color="teal-lighten-1" v-model="selected"
                                :value="item" :indeterminate="item.isReg" :disabled="item.isReg"></v-checkbox-btn>
                        </td>
                        <td class="text-center" :class="{'bg-brown-lighten-5':item.isReg }">{{ item.number }}</td>
                        <td class="pt-1" :class="{'bg-brown-lighten-5':item.isReg }">
                            <span class="text-medium-emphasis">{{ item.en_name }}</span>
                            <v-text-field v-model="item.name" :disabled="item.isReg"></v-text-field>
                        </td>
                        <td :class="{'bg-brown-lighten-5':item.isReg }">
                            <span class="text-body-large" v-if="item.isReg">{{ item.promotype.name }}</span>
                            <PromoDropdown class="pt-6" v-model:id="item.promotype.id" v-model:setcode="attr" v-else></PromoDropdown>
                        </td>
                        <td class="text-center" :class="{'bg-brown-lighten-5':item.isReg }">
                            <v-icon v-if="!item.isReg" icon="mdi-minus" color="grey-darken-1"></v-icon>
                            <v-icon v-else  icon="mdi-circle-outline" color="grey-darken-1"></v-icon>
                        </td>
                    </template>
                </paginated-table>
            </v-tabs-window-item>
        </v-tabs-window>
        </v-sheet>
        <div class="text-center mt-6">
            <div class="three wide column">
                <ModalButton @action="store" v-model="disabled">DBに登録する</ModalButton>
            </div>
        </div>
    </article>
    <progress-bar v-model:visible="dialog" :total="selected.length" :completed="finCount"></progress-bar>
</template>
