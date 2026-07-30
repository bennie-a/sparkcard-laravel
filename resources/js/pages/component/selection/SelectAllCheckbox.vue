<script setup>
import { computed } from 'vue';

    const selected = defineModel("selected");
    const result = defineModel("result");

    const isAll = computed({
        get() {
            return result.value.length > 0 &&  selected.value.length === result.value.length;
        },
        set(value) {
            selected.value = value ? [...result.value] : [];
        }
    });
</script>
<template>
    <v-checkbox-btn color="teal-lighten-1" v-model="isAll"></v-checkbox-btn>
</template>
