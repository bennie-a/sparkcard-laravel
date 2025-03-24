<script setup>
import { computed, onMounted } from "vue";

const name = defineModel({required:true});

const colorkind = [
                    { "color":"purple", "label":"オリジナルパック"},
                    {"color":"green", "label":"私物"},
                    {"color":"orange", "label":"買取"},
                    {"color":"grey", "label":"棚卸し"},
                    {"color":"brown", "label":"返品"}
                ];
const color = computed(() => {
    const item = colorkind.find(c => c.label == name.value);
    return item.color;
});
</script>
<template>
    <label class="ui label basic" :class="color">{{name}}</label><span class="ml-half"></span>
</template>