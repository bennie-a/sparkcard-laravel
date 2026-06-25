<script setup>
import { useDate } from 'vuetify';

const selectedDate = defineModel('selectedDate', {type:Array, required:true});
const duration = defineModel('duration', {type:Number, required:true});

const adapter = useDate();
const today = adapter.date();

function daysAgo(duration) {
    return adapter.addDays(today, -duration);
}

selectedDate.value = [daysAgo(duration), today];
</script>
<template>
    <v-date-input v-model="selectedDate"
    multiple="range"
     label="入荷日"
     variant="outlined"
    prepend-icon=""
    prepend-inner-icon="mdi-calendar-expand-horizontal"
></v-date-input>
</template>
