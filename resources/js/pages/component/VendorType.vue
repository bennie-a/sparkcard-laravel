<script  setup>
import { onBeforeMount, reactive } from "vue";
import axios from "axios";
const vendorType = defineModel({type:Number, default:1});
const vendorTypeList = reactive([]);
onBeforeMount(async() => {
        // 入荷先カテゴリを取得
        await axios
            .get("/api/vendor")
            .then((response) => {
                vendorTypeList.value = response.data;
            })
            .catch((e) => {
                console.error(e);
            })
});
</script>
<template>
<v-select v-model="vendorType" :items="vendorTypeList.value"
    item-title="name" item-value="id" label="入荷先カテゴリ"></v-select>
</template>
