<script setup>
    import { onMounted, reactive, ref, watch } from 'vue';
    import vendorType from '../component/VendorType.vue';
    import scdatepicker from "../component/SCDatePicker.vue";
    import { useRoute, useRouter } from 'vue-router';
    import Loading from "vue-loading-overlay";
    import { apiService } from "@/component/ApiGetService";
    import condition from "../component/tag/ConditionTag.vue";
    import foiltag from "../component/tag/FoilTag.vue";
    import ModalButton from "../component/ModalButton.vue";
    import { piniaMsgStore } from "@/stores/global/PiniaMsg";
    import {apiPutService} from "@/component/ApiPutService";
    import {arrDateConditionStore} from "@/stores/arrival/arrDateCondition";
    import UseDateFormatter from '../../functions/UseDateFormatter.js';
    import PiniaMsgForm from "../component/PiniaMsgForm.vue";


    const router = useRouter();
    const route = useRoute();
    const isLoading = ref(false);
    const arrival_id = route.params.arrival_id;

    const piniaMsg = piniaMsgStore();

    const toDssPage = () => {
        router.push({
            name: "ArrivalLogDss",
        });
    };

    const detail  = ref({card:{name:""}});
   // 初期表示
    onMounted(async() => {
        piniaMsg.reset();
        isLoading.value = true;
        await apiService.get({
            url: `/arrival/${arrival_id}`,
            onSuccess: (data) => {
                detail.value = data;
                console.log(detail.value);
            },
            onError: (error) => {
                console.error("Error fetching arrival details:", error);
            },
            onFinally: () => {
                isLoading.value = false;
            }
        });
    });

    const {toString} = UseDateFormatter();
    const update = async() => {
        piniaMsg.reset();
        const updateDetail = detail.value;
        const query  = {
            arrival_date: toString(updateDetail.arrival_date),
            cost: updateDetail.cost,
            quantity: updateDetail.quantity,
            vendor_type_id: updateDetail.vendor.id,
            vendor: updateDetail.vendor.supplier};
        isLoading.value = true;
        await apiPutService.put({
            url: `/arrival/${arrival_id}`,
            query: query,
            onSuccess: (data) => {
                arrDateConditionStore().arrivalDate = data.arrival_date;
                piniaMsg.setSuccess("変更しました。");
                toDssPage();
            },
            onFinally: () => {
                isLoading.value = false;
            }
        });
    };
</script>
<template>
    <PiniaMsgForm></PiniaMsgForm>
    <article v-if="!isLoading">
    <div class="ui grid">
        <div class="mt-1 ui seven wide column form">
            <h2 class="ui medium header" v-if="detail.card.foil">
                {{ detail.card.name }}<foiltag :isFoil="detail.card.foil.is_foil" :name="detail.card.foil.name"/>
                <div class="sub header"  v-if="detail.card.exp">
                        {{detail.card.exp.name}}&#91;{{ detail.card.exp.attr }}&#93;&#35;{{ detail.card.number }}
                </div>
            </h2>
            <div class="two fields">
                <div class="two wide field">
                    <label>言語</label>
                    <span> {{ detail.card.lang }}</span>
                </div>
                <div class="field">
                    <label>状態</label>
                        <condition :name="detail.card.condition" v-if="detail.card.condition"></condition>
                </div>
            </div>
            <div class="ui divider"></div>
            <div class="three fields">
                <div class="seven wide field">
                    <label>入荷日</label>
                    <scdatepicker v-model="detail.arrival_date"></scdatepicker>
                </div>
                <div class="four wide field">
                    <label>原価</label>
                    <div class="ui left icon input">
                        <input type="number" step="1" min="1" v-model="detail.cost">
                        <i class="yen sign icon"></i>                       
                    </div>
                </div>
                <div class="four wide field">
                    <label>枚数</label>
                    <div class="ui right labeled input">
                        <input type="number" min="1" step="1" v-model="detail.quantity">
                        <label class="ui basic label">枚</label>
                    </div>
                </div>

            </div>
            <div class="two fields">
                <div class="six wide field">
                    <label>入荷カテゴリ</label>
                    <div v-if="detail.vendor">
                        <vendorType v-model="detail.vendor.id"></vendorType>
                    </div>
                </div>
                <div class="ten wide field">
                    <label>取引先</label>
                    <div v-if="detail.vendor">
                        <input type="text" v-model="detail.vendor.supplier" :disabled="detail.vendor.id !== 3">
                    </div>
                </div>
            </div>
            <div class="two fields">
                <div class="six wide field">
                    <button class="ui basic teal button" @click="toDssPage"><i class="angle double left icon"></i>入荷詳細に戻る</button>
                </div>
                <div class="five wide field">
                    <ModalButton :msg="`変更してもよろしいですか？`" @action="update()"><i class="pencil alternate icon"></i>変更する</ModalButton>
                </div>
            </div>
        </div>
        <div class="six wide column">
            <img class="ui medium image" :src="detail.card.image_url" alt="">
        </div>
    </div>
    </article>

        <loading
         :active="isLoading"
         :can-cancel="false" :is-full-page="true" />
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