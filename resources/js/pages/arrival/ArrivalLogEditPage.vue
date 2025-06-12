<script setup>
    import { onMounted, reactive, ref } from 'vue';
    import vendorType from '../component/VendorType.vue';
    import scdatepicker from "../component/SCDatePicker.vue";
    import lang from "../component/Language.vue";
    import { useRoute, useRouter } from 'vue-router';
    import Loading from "vue-loading-overlay";
    import { apiService } from "@/component/ApiGetService";
    import condition from "../component/tag/ConditionTag.vue";
    import foiltag from "../component/tag/FoilTag.vue";

    const router = useRouter();
    const route = useRoute();
    const vendorNum = ref(2);
    const isLoading = ref(false);
    const arrival_id = route.params.arrival_id;
    const toDssPage = () => {
        console.log(detail.value.vendorId);
        router.push({
            name: "ArrivalLogDss",
            params: { arrival_date: detail.value.arrivalDate, vendor_id:detail.value.vendorId},
        });
    };

    const detail  = ref({card:{name:""}});
   // 初期表示
    onMounted(async() => {
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
        // detail.value = {lang:"EN", arrivalDate: ref(new Date(route.params.arrival_date)), vendorId:3};
    });
</script>
<template>
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
                        <input type="text" v-model="detail.vendor.supplier">
                    </div>
                </div>
            </div>
            <div class="two fields">
            </div>
            <div class="field">
                <button class="ui basic teal button" @click="toDssPage"><i class="angle double left icon"></i>入荷詳細に戻る</button>
                <button class="ui teal button ml-1"><i class="pencil alternate icon"></i>変更する</button>                    
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