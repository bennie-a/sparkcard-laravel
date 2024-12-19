<script setup>
    import { onMounted, ref } from 'vue';
    import vendorType from '../component/VendorType.vue';
    import scdatepicker from "../component/SCDatePicker.vue";
    import lang from "../component/Language.vue";

    const detail = ref({});
    const vendorNum = ref(2);
    onMounted(() => {
        detail.value = {lang:"EN", arrivalDate: ref(new Date())};
    });
</script>
<template>
    <div class="ui grid">
        <div class="mt-1 ui seven wide column form">
            <div class="field">
                <label>カード名</label>
                <div class="emphasis">
                    アスモラノマルディカダイスティナカルダカール
                </div>
                <span class="setname">モダンホライゾン2[MH2]</span>
            </div>
            <div class="two fields">
                <div class="seven wide field">
                    <label>入荷日</label>
                    <scdatepicker v-model="detail.arrivalDate"></scdatepicker>
                </div>
                <div class="field">
                    <label>言語</label>
                    <lang v-model="detail.lang"></lang>
                </div>
            </div>
            <div class="two fields">
                <div class="six wide field">
                    <label>入荷カテゴリ</label>
                    <vendorType v-model="vendorNum"></vendorType>
                </div>
                <div class="ten wide field">
                    <label>取引先</label>
                    <input type="text">
                </div>
            </div>
            <div class="two fields">
                <div class="four wide field">
                    <label>原価</label>
                    <div class="ui left icon input">
                        <input type="number" step="1" min="1">
                        <i class="yen sign icon"></i>                       
                    </div>
                </div>
                <div class="four wide field">
                    <label>枚数</label>
                    <div class="ui right labeled input">
                        <input type="number" min="1" step="1">
                        <label class="ui basic label">枚</label>
                    </div>
                </div>
            </div>
            <div class="field">
                <button class="ui basic teal button"><i class="angle double left icon"></i>入荷詳細に戻る</button>
                <button class="ui teal button ml-1"><i class="pencil alternate icon"></i>変更する</button>                    
            </div>
        </div>
        <div class="six wide column">
            <img class="ui medium image" src="https://cards.scryfall.io/png/front/d/8/d80ff101-cfcd-4ffd-a8d4-f47d7d5048d2.png?1646240439" alt="">
        </div>
    </div>
</template>
<style scoped>

/* 強調 */
.emphasis {
    font-weight: 500;
}

.setname {
    color:var(--gray);
}
</style>