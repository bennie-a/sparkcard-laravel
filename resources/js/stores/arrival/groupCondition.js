import {ref} from 'vue';
import {defineStore} from 'pinia';
export const groupConditionStore = defineStore('groupConditionStore', () => {

    const fname = ref('User');
    const lname = ref('');

    const startDate = ref("");
    const endDate = ref("");

    return {startDate, endDate}
});