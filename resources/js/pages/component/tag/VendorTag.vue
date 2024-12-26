<script setup>
import { computed, onMounted } from "vue";

const vendorId = defineModel({required:true});

const colorkind = [
                    {"id":1, "color":"purple", "label":"オリジナルパック"},
                    {"id":2, "color":"green", "label":"私物"},
                    {"id":3, "color":"orange", "label":"買取"},
                    {"id":4, "color":"grey", "label":"棚卸し"},
                    {"id":5, "color":"brown", "label":"返品"}
                ];
const color = computed(() => {
    const item = colorkind.find(c => c.id == vendorId.value);
    return item;
});
</script>
<template>
    <label class="ui label basic" :class="color.color">{{color.label}}</label><span class="ml-half"></span>
</template>