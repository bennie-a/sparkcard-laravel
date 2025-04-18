import {ref} from 'vue';
import {defineStore} from 'pinia';
export const groupConditionStore = defineStore('groupConditionStore', () => {
    const startDate = ref(null);
    const endDate = ref(null);
    const itemname = ref("");

    // 検索条件を初期化する。
    function reset() {
        const now = new Date();
        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(now.getDate() - 7);

        startDate.value = sevenDaysAgo;
        endDate.value = now;
        itemname.value = "";
    }

    return {startDate, endDate, itemname, reset}
});