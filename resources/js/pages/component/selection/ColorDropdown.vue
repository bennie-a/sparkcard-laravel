<script setup>
const selectedColor = defineModel({type:String, default:""});
const colorItems = [
    {state:'白', item_value:'W', color:'yellow-lighten-1'},
    {state:'青', item_value:'U', color:'blue-lighten-1'},
    {state:'黒', item_value:'B', color:'grey-darken-3'},
    {state:'赤', item_value:'R', color:'red-lighten-1'},
    {state:'緑', item_value:'G', color:'green-lighten-1'},
    {state:'多色', item_value:'M', color:'orange-lighten-1'},
    {state:'無色', item_value:'L', color:'purple-lighten-1'},
    {state:'アーティファクト', item_value:'A', color:'blue-grey-lighten-1'},
    {state:'土地', item_value:'Land', color:'brown-lighten-1'},
    {state:'アートカード', item_value:'Art', color:'pink-lighten-1'},
];

</script>
<template>
    <v-select
    label="色"
    v-model="selectedColor"
    :items="colorItems"
    item-title="state" item-value="item_value" clearable>
</v-select>

</template>
