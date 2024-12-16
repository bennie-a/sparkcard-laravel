<script setup>
import { onBeforeMount, reactive } from "vue";
import axios from "axios";
const vendorTypeList = reactive([]);
const vendorType = 1;
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
<select v-model="vendorType" class="mr-1 ui dropdown">
    <option v-for="t in vendorTypeList.value" :key="t.id" :value="t.id">{{t.name }}</option>
</select>
</template>