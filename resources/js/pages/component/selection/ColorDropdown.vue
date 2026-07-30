<script setup>
import RequiredLabel from '../label/RequiredLabel.vue';
import { ColorMaster } from '../const/ColorMaster.js';

const selectedColor = defineModel({type:String, default:""});
defineProps({
    required: {
        type: Boolean,
        default: false
  }
});

</script>
<template>
    <v-select
    label="色"
    v-model="selectedColor"
    :items="ColorMaster.list"
    item-title="state" item-value="key" clearable>
    <template v-slot:label v-if="required">
        <RequiredLabel text="色" required></RequiredLabel>
    </template>
</v-select>

</template>
