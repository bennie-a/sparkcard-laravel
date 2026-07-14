<script setup>
    import { computed, ref} from "vue";
    import UseDateFormatter from "../../../functions/UseDateFormatter";

    const model = defineModel('selectedDate', {type:String, required:true});
    defineProps({'datelabel': {type:String, required:true}});

    const {toString, toDate} = UseDateFormatter();
    const formatDate = computed({
        get() {
            return model.value ? toDate(model.value) : null;
        },
        set(value) {
            model.value = value ? toString(value) : '';
        }
    });

</script>
<template>
    <v-date-input
    v-model="formatDate"
    :label="datelabel"
    prepend-inner-icon="mdi-calendar-today"
    />
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
