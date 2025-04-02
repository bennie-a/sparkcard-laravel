<script setup>
import { computed, onMounted } from "vue";

const vendor = defineModel({required:true});

const colorkind  = {
                                    1:"purple",
                                    2:"green",
                                    3:"orange",
                                    4:"grey",
                                    5:"brown"
                                };
const color = computed(() => {
    return colorkind[vendor.value.id];
});
</script>
<template>
    <label class="ui label basic" :class="color">{{vendor.name}}</label><span class="ml-half"></span>
</template>