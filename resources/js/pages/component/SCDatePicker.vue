<script setup>
import datepicker from "@vuepic/vue-datepicker";
import { ref} from "vue";
const selectedDate = defineModel('selectedDate', {type:Date, required:true});
defineProps({'datelabel': {type:String, required:true}});

// 日付をYYYY/MM/dd形式に整形する。
const dateFormat = (date) => {
    const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}/${month}/${day}`;
}
</script>
<template>
    <v-date-input
    v-model="selectedDate"
    :label="datelabel"
    variant="outlined"
    input-format="yyyy/mm/dd"
    prepend-icon=""
    prepend-inner-icon="mdi-calendar-today"
    />
</template>
<style>
.dp__input {
    padding-left: 2rem!important;
}
</style>
