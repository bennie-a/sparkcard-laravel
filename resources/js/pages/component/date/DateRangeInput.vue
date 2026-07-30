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
        if (!start.value && !end.value) {
            return [];
        }
            return [start.value, end.value];
        },
        set(value) {
        if (!start.value && !end.value) {
            start.value = null;
            end.value = null;
        }
            start.value = value?.[0],
            end.value = value?.[value.length - 1]
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

const clear = () => {
    console.log("clear", start.value, end.value);
    range.value = [];
    start.value = null;
    end.value = null;
}

</script>
<template>
    <v-date-input v-model="range"
    multiple="range"
     :label="props.label"
    prepend-inner-icon="mdi-calendar-expand-horizontal"
    clearable
    @click:clear="clear"
    cancel-text="キャンセル"
    placeholder="日付を選択"
></v-date-input>
</template>
