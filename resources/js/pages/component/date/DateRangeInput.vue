<script setup>
import { computed } from 'vue';
import { useDate } from 'vuetify';

const start = defineModel('start', {type:Date, required:false});
const end = defineModel('end', {type:Date, required:false});
const props = defineProps(
    {
        'days':{type:Number, default:7},
        'label':{type:String, required:true}
    }
);

const range = computed({
        get() {
            return [start.value, end.value];
        },
        set(value) {
            start.value = value?.[0] ?? null,
            end.value = value?.[value.length - 1] ?? null
        }
    }
);

const adapter = useDate();
const today = adapter.date();

if (!start.value && !end.value) {
    start.value = daysAgo(props.days);
    end.value = today;
}

function daysAgo(days) {
    return adapter.addDays(today, -days);
}

</script>
<template>
    <v-date-input v-model="range"
    multiple="range"
     :label="props.label"
    prepend-inner-icon="mdi-calendar-expand-horizontal"
    clearable
></v-date-input>
</template>
