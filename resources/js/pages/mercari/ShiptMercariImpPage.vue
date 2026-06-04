<script setup>

    import { onMounted, reactive, ref } from 'vue';
    import FileUpload from "../component/FileUpload.vue";
    import Loading from "vue-loading-overlay";
    import axios from 'axios';
    import condition from "../component/tag/ConditionTag.vue";
    import cardlayout from "../component/CardLayout.vue";
    import ModalButton from '../component/modal/ModalButton.vue';
    import PiniaMsgForm from '../component/PiniaMsgForm.vue';
    import scdatepicker from "../component/SCDatePicker.vue";
    import ListPagination from "@/pages/component/pagination/ListPagination.vue";
    import { usePagenate } from "@/pages/component/pagination/UsePaginate";
    import {MsgStore} from "@/pages/component/msg/MsgStore";

    const msgStore = MsgStore();

    const result = reactive([]);
    const resultCount = ref(0);
    const isLoading = ref(false);

    const shiptDate = ref(new Date());

    const {
        page, pageCount, paginatedList, resetPage
    } = usePagenate(result, 4);

    /**
     * インポート実行
     */
    const post = async function() {
        isLoading.value = true;
        await Promise.all(result.value.map(async (r) => {
            const formatShiptDate = shiptDate.value.toLocaleDateString("ja-JP", {year: "numeric",month: "2-digit",
            day: "2-digit"})
            const json =
                {
                    order_id: r.order_id,
                    shipping_date: formatShiptDate,
                    buyer_name: r.buyer_name,
                    zip_code: r.zip_code,
                    address: r.address,
                    items: []
                };
            json.items = r.items.map((i) => {
                return {
                    id: i.stock.id,
                    shipment: i.shipment,
                    single_price: i.single_price,
                    total_price: i.total_price,
                    isRegistered: i.isRegistered
                };
            });

            await axios.post('/api/shipping', json)
                .then((response) => {
                    console.log('Imported:', response.data);
                }).catch((e) => {
                    console.log('Import Error:', e.response.data);
                });
            }));
        isLoading.value = false;
        msgStore.success('インポートが完了しました。');
    };

    const uploadFile = async(file) => {
        resetPage();
        msgStore.clear();
        isLoading.value = true;
        result.value = [];
        const formData = new FormData();
        formData.append('file', file);

        await axios.post('/api/shipping/parse', formData,{
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then((response) => {
                result.value = response.data;
                resultCount.value = result.value.length;
            }).catch((e) => {
                let error = e.response.data;
                let errRows = error.rows.map(function(r) {
                    return `${r.row}行目: ${r.msg}`;
                });
                console.log('Error:', error);
                msgStore.error(errRows);
            }).finally(() => {
                isLoading.value = false;
            });
    };

</script>

<template>
    <v-form rounded class="form_sheet pa-4">
        <div class="w-50">
            <FileUpload type="csv" @action="uploadFile"/>
        </div>
    </v-form>
    <section class="mt-10" v-if="resultCount > 0">
        <h2 class="text-title-medium"
        >
           件数： {{ resultCount }}件
        </h2>
        <v-form class="form_sheet pt-6 pl-6">
                <v-row>
                    <v-col cols="3">
                        <scdatepicker v-model:selectedDate="shiptDate" datelabel="発送日"></scdatepicker>
                    </v-col>
                    <v-col>
                        <ModalButton  @action="post()">インポート</ModalButton>
                    </v-col>
                </v-row>
        </v-form>
        <article>
            <v-sheet class="pa-4 mt-4" border rounded v-for="r in paginatedList" :key="r.order_id">
                <span class="text-body-small">{{ r.order_id }}</span>
                <v-row class="mt-3" gap="25">
                    <v-col cols="4">
                        <address id="buyer" class="pa-3 text-body-medium form_sheet">
                            <span>&#12306;{{ r.zip_code }}</span>
                            <p>{{ r.address }}</p>
                            <span class="text-title-medium font-weight-bold">{{ r.buyer_name }}様</span>
                        </address>
                    </v-col>
                    <v-col id="price" class="text-body-medium" cols="2">
                        <div  class="d-flex justify-space-between mb-1">
                            <span class="entry">送料</span>
                            <span>&yen;{{ r.shipping_fee }}</span>
                        </div>
                        <div class="d-flex justify-space-between mb-1">
                            <span class="entry">クーポン割引</span>
                                <span>
                                    &yen;{{ r.coupon_discount_amount }}
                                </span>
                        </div>
                           <v-divider  :thickness="1" class="border-opacity-50 my-2"  variant="dashed"></v-divider>
                        <div class="d-flex justify-space-between mb-1">
                            <span>合計金額</span>
                            <span>&yen;{{ r.total_price }}</span>
                        </div>
                    </v-col>
                </v-row>
                <v-table  class="mt-4 border-thin">
                <thead>
                    <tr class="bg-grey-lighten-3">
                        <th width="10%" class="text-left">在庫ID</th>
                        <th class="eight wide">カード情報</th>
                        <th  width="10%" class="text-center">状態</th>
                        <th  width="10%" class="text-center">枚数</th>
                        <th width="10%" class="text-center">単価</th>
                        <th width="10%" class="text-center">小計</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, idx) in r.items" :key="idx">
                        <td  class="text-left">{{ item.stock.id }}</td>
                        <td><cardlayout v-model:card="item.stock.card" v-model:lang="item.stock.lang"/></td>
                        <td class="text-center"><condition :name="item.stock.condition"/></td>
                        <td class="text-center">{{ item.shipment }}枚</td>
                        <td class="text-center">&yen;{{ item.single_price }}</td>
                        <td class="text-center">&yen;{{ item.total_price }}</td>
                    </tr>
                </tbody>
                </v-table>
            </v-sheet>
        </article>
        <ListPagination v-model="page" :length="pageCount"></ListPagination> <!-- ページネーションコンポーネント -->
    </section>
    <loading
    :active="isLoading"
         :can-cancel="false" :is-full-page="true" />

</template>
