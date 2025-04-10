import {ref} from 'vue';
import {defineStore} from 'pinia';
export const groupConditionStore = defineStore('groupConditionStore', () => {
    const startDate = ref("");
    const endDate = ref("");

    function reset() {
        startDate.value = "";
        endDate.value = "";
    }

    return {startDate, endDate, reset}
});