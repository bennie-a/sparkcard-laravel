<script setup>
    import { onMounted, reactive, ref, watch } from 'vue';
    import vendorType from '../component/VendorType.vue';
    import scdatepicker from "../component/date/DateInput.vue";
    import { useRoute, useRouter } from 'vue-router';
    import Loading from "vue-loading-overlay";
    import { apiService } from "@/component/ApiGetService";
    import condition from "../component/tag/ConditionTag.vue";
    import foiltag from "../component/tag/FoilTag.vue";
    import ColorTag from "../component/tag/ColorTag.vue";
    import ModalButton from "../component/modal/ModalButton.vue";
    import {apiPutService} from "@/component/ApiPutService";
    import {arrDateConditionStore} from "@/stores/arrival/arrDateCondition";
    import UseDateFormatter from '../../functions/UseDateFormatter.js';
    import { MsgStore } from "../component/msg/MsgStore.js";
    import {LoadStore} from "@/stores/loading/LoadStore.js";
    import CardLayout from '../component/CardLayout.vue';

    const router = useRouter();
    const route = useRoute();
    const arrival_id = route.params.arrival_id;

    const msgStore = MsgStore();
    const loadStore = LoadStore();
    const arrivalDate = ref(new Date());
    const supplier = ref('');
    const cost = ref(1);

    const toDssPage = () => {
        router.push({
            name: "ArrivalLogDss",
        });
    };

    const detail  = ref({});
   // 初期表示
    onMounted(async() => {
        msgStore.clear();
        loadStore.on();
        await apiService.get({
            url: `/arrival/${arrival_id}`,
            onSuccess: (data) => {
                detail.value = data;
                arrivalDate.value = new Date(data.arrival_date);
                supplier.value = data.vendor.supplier;
                cost.value = data.cost;
                console.log(detail.value);
            },
            onError: (error) => {
                console.error("Error fetching arrival details:", error);
            },
            onFinally: () => {
                loadStore.off();
                console.log("Finished fetching arrival details.");
            }
        });
    });

    const {toString} = UseDateFormatter();
    const update = async() => {
        loadStore.on();
        msgStore.clear();
        const updateDetail = detail.value;
        const query  = {
            arrival_date: toString(arrivalDate.value),
            cost: cost.value,
            quantity: updateDetail.quantity,
            vendor_type_id: updateDetail.vendor.id,
            vendor: supplier.value};
        await apiPutService.put({
            url: `/arrival/${arrival_id}`,
            query: query,
            onSuccess: (data) => {
                arrDateConditionStore().arrivalDate = data.arrival_date;
                arrDateConditionStore().vendorId = data.vendor.id;
                msgStore.success("変更しました。");
                // toDssPage();
            },
            onFinally: () => {
                loadStore.off();
            }
        });
    };

    const clearSupplier = (vendorId) => {
        if (vendorId === 2) {
            cost.value = 1;
        } else {
            cost.value = detail.value.cost;
        }
        if (vendorId !== 3) {
            supplier.value = '';
        } else {
            supplier.value = detail.value.vendor.supplier;
        }
    };
</script>
<template>
    <article v-show="loadStore.isInActive()">
        <v-table class="w-75 item_list border-thin">
            <thead>
                <tr>
                    <th width="10%" class="text-center">在庫ID</th>
                    <th>カード情報</th>
                    <th width="15%" class="text-center">色</th>
                    <th width="15%" class="text-center">状態</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">{{ detail.stock_id }}</td>
                    <td><CardLayout :card="detail.card" v-if="detail.card"></CardLayout></td>
                     <td class="text-center">
                        <ColorTag :type="detail.card.color" v-if="detail.card"></ColorTag>
                     </td>
                    <td class="text-center"> <condition :name="detail.condition" v-if="detail.condition"></condition></td>
                </tr>
            </tbody>
        </v-table>
        <v-form class="form_sheet mt-6 pa-4">
            <v-row class="mt-2" v-if="detail.vendor">
                <v-col cols="3">
                    <vendorType v-model="detail.vendor.id" @action="clearSupplier"></vendorType>
                </v-col>
                <v-col cols="4">
                    <v-text-field label="取引先" v-model="supplier" :disabled="detail.vendor.id !== 3" clearable></v-text-field>
                </v-col>
                <v-col cols="3">
                    <scdatepicker v-model:selectedDate="arrivalDate" datelabel="入荷日"></scdatepicker>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="2">
                    <v-text-field
                        v-model="cost"
                        type="number"
                        step="1"
                        min="1"
                        label="原価"
                        prefix="¥"></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field
                        v-model="detail.quantity"
                        type="number"
                        step="1"
                        min="1"
                        label="枚数"
                        suffix="枚"></v-text-field>
                </v-col>
            </v-row>
        </v-form>
        <div class="text-center mt-6">
            <v-btn variant="outlined" color="teal-lighten-1" @click="toDssPage" class="mr-4">
                <v-icon icon="mdi mdi-chevron-double-left"></v-icon>入荷詳細に戻る
            </v-btn>
            <ModalButton :msg="`変更してもよろしいですか？`" @action="update()"><v-icon icon="mdi mdi-pencil"></v-icon>変更する</ModalButton>
        </div>
    <div class="ui grid">
        <div class="mt-1 ui seven wide column form">
            <div class="two fields">
                <div class="six wide field">
                </div>
                <div class="five wide field">
                </div>
            </div>
        </div>
    </div>
    </article>
</template>
<style scoped>

/* 強調 */
.emphasis {
    font-weight: 700;
    font-size: 1.2em;
}

.setname {
    color:var(--gray);
}
</style>
