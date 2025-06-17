import { defineStore } from 'pinia';
import { ref } from 'vue';
export const arrDateConditionStore = defineStore('arrDateConditionStore', () => {
    const arrivalDate = ref(null);
    const vendorId = ref(0);

    // 検索条件を初期化する。
    
    function reset() {
        arrivalDate.value = null;
        vendorId.value = 0;
    }

    return {arrivalDate, vendorId, reset}
});