<script setup>
import { useDate } from 'vuetify';

const selectedDate = defineModel('selectedDate', {type:Array, required:true});
const props = defineProps(
    {
        'days':{type:String, required:true},
        'label':{type:String, required:true}
    }
);

const adapter = useDate();
const today = adapter.date();

function daysAgo(days) {
    return adapter.addDays(today, -days);
}

selectedDate.value = [daysAgo(props.days), today];
</script>
<template>
    <v-date-input v-model="selectedDate"
    multiple="range"
     :label="props.label"
    prepend-inner-icon="mdi-calendar-expand-horizontal"
    clearable
></v-date-input>
</template>
