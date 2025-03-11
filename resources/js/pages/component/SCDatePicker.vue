<script setup>
import datepicker from "@vuepic/vue-datepicker";
import { ref} from "vue";
const newSelectedDate = defineModel({type:Date, required:true});

// 日付をYYYY/MM/dd形式に整形する。
const dateFormat = (date) => {
    const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}/${month}/${day}`;
}
</script>
<template>
        <datepicker
        v-model="newSelectedDate"
        input-class-name="dp_custom_input"
        locale="jp"
        :enable-time-picker="false"
        :format="dateFormat"
        auto-apply
    />
</template>
<style>
.dp__input {
    padding-left: 2rem!important;
}
</style>