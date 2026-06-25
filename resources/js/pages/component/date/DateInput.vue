<script setup>
import { ref} from "vue";
const selectedDate = defineModel('selectedDate', {type:Date, required:true});
defineProps({'datelabel': {type:String, required:true}});

</script>
<template>
    <v-locale-provider locale="ja">
        <v-date-input
        v-model="selectedDate"
        :label="datelabel"
        variant="outlined"
        prepend-icon=""
        prepend-inner-icon="mdi-calendar-today"
        />
    </v-locale-provider>
</template>
<style>
.v-date-picker-month__day--selected .v-btn {
  background-color: #26A69A !important;
  color: white !important;
}

.v-date-picker-month__day--selected .v-btn__content {
  color: white !important;
}
</style>
